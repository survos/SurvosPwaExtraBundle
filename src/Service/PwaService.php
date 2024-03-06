<?php


declare(strict_types=1);

namespace Survos\PwaExtraBundle\Service;

use SpomkyLabs\PwaBundle\Dto\Manifest;
use SpomkyLabs\PwaBundle\Dto\ServiceWorker;
use SpomkyLabs\PwaBundle\Dto\Workbox;
use Symfony\Component\Serializer\SerializerInterface;

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
    public const NetworkFirst='NetworkFirst';
    public const StaleWhileRevalidate='StaleWhileRevalidate';

    public function __construct(
        private string $cacheFilename,
        private ServiceWorker $serviceWorker,
        private Manifest $manifest,
        private SerializerInterface $serializer,
        private array $config=[],
        )
    {
    }

    public static function getTemplate(): ?string
    {
        return 'data_collector/pwa_collector.html.twig';
    }

    public function getManifestData(): array
    {
        // argh, there must be a better way to do this! Normalizer?
        return json_decode($this->serializer->serialize($this->manifest, 'json'), true);
        dd($this->manifest);

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
