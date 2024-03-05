<?php

declare(strict_types=1);

// src/DataCollector/SeoCollector.php
//   from https://www.strangebuzz.com/en/blog/adding-a-custom-data-collector-in-the-symfony-debug-bar
namespace Survos\PwaExtraBundle\DataCollector;

use Survos\PwaExtraBundle\Service\PwaService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

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
        $cachingStrategy = $this->pwaService->getRouteCache()[$route];
        $this->data['cachingStrategy'] = $cachingStrategy;
        $this->data['title'] = '@pwa(title)';
    }

    public function getRoute()
    {
        return $this->data['route'];
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
