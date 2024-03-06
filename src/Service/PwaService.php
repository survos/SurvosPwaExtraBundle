<?php


declare(strict_types=1);

namespace Survos\PwaExtraBundle\Service;

use SpomkyLabs\PwaBundle\Dto\ServiceWorker;
use SpomkyLabs\PwaBundle\Dto\Workbox;

/**
 * PWA related Twig helpers.
 */
final class PwaService
{

    public function __construct(
        private string $cacheFilename,
        private ServiceWorker $serviceWorker,
        private array $config=[]
    )
    {
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/pwa_collector.html.twig';
    }

    public function getCacheInfo()
    {
        $cache = [];
        foreach ($this->getWorkbox() as $var=>$val) {
            if (is_object($val)) {
                $name = $val::class;
                if (str_ends_with($name, 'Cache')) {
                    $cache[] = (array)$val + ['name' => (new \ReflectionClass($name))->getShortName()];
                }
            }
        }
        return $cache;
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
