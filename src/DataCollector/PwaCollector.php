<?php

declare(strict_types=1);

// src/DataCollector/SeoCollector.php
//   from https://www.strangebuzz.com/en/blog/adding-a-custom-data-collector-in-the-symfony-debug-bar
namespace Survos\PwaExtraBundle\DataCollector;

use Survos\PwaExtraBundle\Service\PwaService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

use Symfony\Component\VarDumper\Cloner\Data;
use function Symfony\Component\String\u;

final class PwaCollector extends DataCollector
{

    private const MAX_PANEL_WIDTH = 50;
    private const CLASS_ERROR = 'red';
    private const CLASS_WARNING = 'yellow';
    private const CLASS_OK = 'green';

    public function __construct(private PwaService $pwaService)
    {
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null): void
    {
        $route = $this->data['route'] = $request->get('_route');
        $cachingStrategy = $this->pwaService->getRouteCache()[$route]??'Not Cached Via Attribute';

        $this->data = [
            'workbox_version' => $this->pwaService->getWorkbox()->version,
        ];
        $this->data['cachingStrategy'] = $cachingStrategy;
        $this->data['title'] = '@pwa(title)';
        $this->data['route'] = $request->get('_route');
        $this->data['cacheTable'] = $this->pwaService->getCacheInfo();

        $this->data['routesFromAttributes'] = $this->pwaService->getRouteCache();
//        dd($this->data);

    }

    public function getRoute()
    {
        return $this->data['route'];
    }

    public function getCacheData(): array
    {
        return $this->data['cacheTable'];

    }

    public function getRoutesFromAttributes(): array
    {
        return $this->data['routesFromAttributes'];

    }

    public function get(string $key): string
    {
        return $this->data[$key];
    }

    public function getData(): Data
    {
        return $this->cloneVar($this->data);

    }

    public function getCachingStrategy()
    {
        return $this->data['cachingStrategy'];
    }


    public function getName(): string
    {
        return self::class;
    }
}
