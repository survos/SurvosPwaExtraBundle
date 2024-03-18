<?php

/** generated from /home/tac/ca/survos/packages/maker-bundle/templates/skeleton/bundle/src/Bundle.tpl.php */

namespace Survos\PwaExtraBundle;

use Psr\Container\ContainerInterface;
use SpomkyLabs\PwaBundle\Dto\ServiceWorker;
use Survos\CoreBundle\HasAssetMapperInterface;
use Survos\CoreBundle\Traits\HasAssetMapperTrait;
use Survos\PwaExtraBundle\Attribute\PwaExtra;
use Survos\PwaExtraBundle\CacheWarmer\PwaCacheWarmer;
use Survos\PwaExtraBundle\Command\ConfigureCommand;
use Survos\PwaExtraBundle\DataCollector\PwaCollector;
use Survos\PwaExtraBundle\Service\PwaService;
use Survos\PwaExtraBundle\Twig\Components\ConnectionDetector;
use Survos\PwaExtraBundle\Twig\Components\PwaInstallComponent;
use Survos\PwaExtraBundle\Twig\TwigExtension;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

class SurvosPwaExtraBundle extends AbstractBundle implements CompilerPassInterface, HasAssetMapperInterface
{
    use HasAssetMapperTrait;

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass($this);
    }


    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {

        $builder->autowire(ConfigureCommand::class)
            ->setAutoconfigured(true)
            ->setPublic(true)
            ->addTag('console.command')
            ->setArgument('$bag', new Reference('parameter_bag'))
        ;

        foreach ([ConnectionDetector::class, PwaInstallComponent::class] as $componentClass) {
            $builder->register($componentClass)
                ->setAutowired(true)
                ->setAutoconfigured(true);
        }

        $builder->autowire(PwaService::class)
            ->setArgument('$serviceWorker', new Reference(ServiceWorker::class))
            ->setArgument('$cacheFilename', $this->getCachedDataFilename($builder))
            ->setArgument('$cacheServices', tagged_iterator('spomky_labs_pwa.cache_strategy'))
            ->setArgument('$config', $config);

        $builder->autowire(PwaCollector::class)
            ->setArgument('$pwaService', new Reference(PwaService::class))
            ->addTag('data_collector', [
                'template' => '@SurvosPwaExtra/data_collector/pwa_collector.html.twig'
            ]);


        $builder
            ->autowire('pwa.cache_warmer', PwaCacheWarmer::class)
            ->setArgument('$router', new Reference('router'))
//        ->addTag('container.service_subscriber', ['id' => 'pwa_service'])
            ->addTag('kernel.cache_warmer', ['priority' => 100]);


        $definition = $builder
            ->autowire('survos.pwa_twig', TwigExtension::class)
            ->addTag('twig.extension')
            ->setArgument('$pwaService', new Reference(PwaService::class));

    }

    public function getPaths(): array
    {
        $dir = realpath(__DIR__ . '/../assets/');
        assert(file_exists($dir), $dir);
        return [$dir => '@survos/pwa-extra'];

    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
            ->scalarNode('stimulus_controller')->defaultValue('@survos/pwa-extra-bundle/detector')->end();
    }


    private function getCachedDataFilename(ContainerBuilder $container)
    {
        $kernelCacheDir = $container->getParameter('kernel.cache_dir');
        return $kernelCacheDir . '/pwa_routes.json';
    }

    // The compiler pass
    public function process(ContainerBuilder $container): void
    {
        $cachingStrategyByRoute = [];
        $cachingStrategyByMethod = [];
        $taggedServices = $container->findTaggedServiceIds('container.service_subscriber');
//        $router = $container->getServiceIds()
//        $router = $this->container->get('router');
//        dd($router->getRouteCollection());


        foreach (array_keys($taggedServices) as $controllerClass) {
            if (!class_exists($controllerClass)) {
                continue;
            }
            $reflectionClass = new \ReflectionClass($controllerClass);
            $requirements = [];
            $classCacheStrategy = null; // the default
            // these are at the controller level, so they apply to all methods
            foreach ($reflectionClass->getAttributes(PwaExtra::class) as $attribute) {
                $args = $attribute->getArguments();
                $classCacheStrategy = $args['cacheStrategy'];
            }
            foreach ($reflectionClass->getMethods() as $method) {
                // there can be nore then one route on a method, but not more than one PwaExtra
                foreach ($method->getAttributes(Route::class) as $attribute) {
                    $args = $attribute->getArguments();
                    $routeName = $args['name'] ?? $method->getName();
                    // get the regex
//                    $details =
                    if ($classCacheStrategy) {
                        $cachingStrategyByMethod[$routeName] = $classCacheStrategy;
                        $cachingStrategyByRoute[$routeName] = $classCacheStrategy;
                    }
                }

                $methodRequirements = [];
                foreach ($method->getAttributes(PwaExtra::class) as $attribute) {
                    $args = $attribute->getArguments();
                    $methodClassStrategy = $args['cacheStrategy'];
                    $cachingStrategyByRoute[$routeName] = $methodClassStrategy;
                }

                // now get the route name(s) and associated the requirements by name.
            }
        }
        file_put_contents($fn = $this->getCachedDataFilename($container), json_encode($cachingStrategyByRoute));

        if (false === $container->hasDefinition('twig')) {
            return;
        }
        $def = $container->getDefinition('twig');

    }


}
