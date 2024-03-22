<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Survos\PwaExtraBundle\CacheWarmer;

use Psr\Container\ContainerInterface;
use Survos\PwaExtraBundle\Service\PwaService;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmerInterface;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

/**
 * Generates the router matcher and generator classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class PwaCacheWarmer implements CacheWarmerInterface, ServiceSubscriberInterface
{
    private ContainerInterface $container;

    public function __construct(private RouterInterface $router) // ContainerInterface $container)
    {
        // As this cache warmer is optional, dependencies should be lazy-loaded, that's why a container should be injected.
//        $this->container = $container;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null): array
    {
        return [];


        if ($route = $this->router->getRouteCollection()->get('app_voyage_show')) {
            dd($buildDir, $route, $route->compile()->getRegex());

            dd($this->router->getRouteCollection());
            $router = $this->container->get('router');
            dd($router->getRouteCollection());
        }
        return [];
//
//        if ($router instanceof WarmableInterface) {
//            return (array) $router->warmUp($cacheDir, $buildDir);
//        }

//        throw new \LogicException(sprintf('The router "%s" cannot be warmed up because it does not implement "%s".', get_debug_type($router), WarmableInterface::class));
    }

    public function isOptional(): bool
    {
        return false;
    }

    public static function getSubscribedServices(): array
    {
        return [
            'pwaService' => PwaService::class,
        ];
    }
}
