<?php


declare(strict_types=1);

namespace Survos\PwaExtraBundle\Service;

/**
 * PWA related Twig helpers.
 */
final class PwaService
{

    public function __construct(
        private string $cacheFilename,
        private array $config=[]
    )
    {
    }

    public function getConfigValue(string $key)
    {
        return $this->config[$key]; // @todo: error checking
    }

    public function getRouteCache()
    {
        return json_decode(file_get_contents($this->cacheFilename), true);

    }

    public function getMinMax(string $key): array
    {
        return [$this->getConfigValue('min' . $key . 'Length'), $this->getConfigValue('max' . $key . 'Length')];
    }

}
