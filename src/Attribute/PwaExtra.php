<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Survos\PwaExtraBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class PwaExtra
{
    public function __construct(
        private ?string $cacheStrategy = null
    )
    {

    }

    public function getCacheStrategy(): ?string
    {
        return $this->cacheStrategy;
    }
}
