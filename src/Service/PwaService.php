<?php


declare(strict_types=1);

namespace Survos\PwaExtraBundle\Service;

use PHPStan\BetterReflection\Reflection\Adapter\ReflectionClass;
use SpomkyLabs\PwaBundle\Dto\Manifest;
use SpomkyLabs\PwaBundle\Dto\ServiceWorker;
use SpomkyLabs\PwaBundle\Dto\Workbox;
use SpomkyLabs\PwaBundle\Service\CacheStrategy;
use SpomkyLabs\PwaBundle\Service\HasCacheStrategies;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Yaml\Yaml;

// NetworkOnly, CacheOnly, CacheFirst, NetworkFirst, or StaleWhileRevalidate

/**
 * PWA related Twig helpers.
 */
final class PwaService
{

    // these probably should go in an interface or in the PWA bundle somewhere.
    public const NetworkOnly = 'NetworkOnly';
    public const CacheOnly = 'CacheOnly';
    public const CacheFirst = 'CacheFirst';
    public const NetworkFirst = 'NetworkFirst';
    public const StaleWhileRevalidate = 'StaleWhileRevalidate';

    public function __construct(
        private string                                                                $cacheFilename,
        private ServiceWorker                                                         $serviceWorker,
        private Manifest                                                              $manifest,
        private NormalizerInterface                                                   $normalizer,
        private SerializerInterface                                                   $serializer,
        private RouterInterface                                                       $router,
        #[TaggedIterator('spomky_labs_pwa.cache_strategy')] private readonly iterable $cacheServices,
        private array                                                                 $config = [],
    )
    {
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/pwa_collector.html.twig';
    }

    /**
     * @return iterable<CacheStrategy>
     */
    public function getCacheServices(): iterable
    {
        return $this->cacheServices;
    }

    public function getManifestData(): array
    {
        // argh, there must be a better way to do this! Normalizer?
        return json_decode($this->serializer->serialize($this->manifest, 'json'), true);
        dd($this->manifest);

    }

    public function getRouteDetails(string $route)
    {
        $routes = $this->router->getRouteCollection();
        dd($routes);


    }

    public function getCacheInfo()
    {

        // this is just a map to make it easier to debug
        $services = [];
        foreach ($this->cacheServices as $service) {
            $strategies = $service->getCacheStrategies();
            $services[(new \ReflectionClass($service::class))->getShortName()] = $this->normalizer->normalize($strategies, null);
        }
//        dd($services);
        return $services;

        $strategies = $service->getCacheStrategies();
        dd($this->normalizer->normalize($strategies, null));
        foreach ($strategies as $strategy) {
            dd($this->normalizer->normalize($strategy, null));
            dd($service, $strategy);
            $cache[] = [
                $strategy->name,
                $strategy->strategy,
                $strategy->urlPattern,
                $strategy->enabled ? 'Yes' : 'No',
                $strategy->requireWorkbox ? 'Yes' : 'No',
                Yaml::dump($strategy->options),
            ];
        }
        dd($this->normalizer->normalize($this->cacheServices, null));

        $cache = [];
        foreach ($this->cacheServices as $cacheStrategy) {
            assert($cacheStrategy instanceof HasCacheStrategies);
            foreach ($cacheStrategy->getCacheStrategies() as $strategy) {
                $cacheStrategies[$strategy->name][] = $strategy;
                dump(cacheStrategy: $cacheStrategy, strategy: $strategy);
            }
        }
//        dd($cacheStrategies);
        dd($this->getWorkbox());
        foreach ($this->getWorkbox() as $var => $val) {
            if (is_object($val)) {
                $className = $val::class;
                $name = $val->cacheName ?? null;
//                dd($val, $cacheStrategies[$name]);
                if (str_ends_with($className, 'Cache')) {
                    $cache[] = (array)$val + [
                            'strategies' => $name ? $cacheStrategies[$name] : [],
                            'shortName' => (new \ReflectionClass($className))->getShortName()];
                }
            }
        }
        return $cache;
    }

    public function getReferenceUrl(string $strategy): string
    {
        return 'https://developer.chrome.com/docs/workbox/modules/workbox-strategies#' . $strategy;
    }

    public function getConfigValue(string $key)
    {
        return $this->config[$key]; // @todo: error checking
    }

    public function getRouteCache(): array
    {
        return json_decode(file_get_contents($this->cacheFilename), true);
    }

    public function getWorkbox(): Workbox
    {
        return $this->serviceWorker->workbox;

    }

    public function getMinMax(string $key): array
    {
        return [$this->getConfigValue('min' . $key . 'Length'), $this->getConfigValue('max' . $key . 'Length')];
    }

}
