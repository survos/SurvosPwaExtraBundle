<?php

namespace Survos\PwaExtraBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name: 'ConnectionDetector', template:'@SurvosPwaExtra/components/ConnectionDetector.html.twig')]
final class ConnectionDetector
{
    public function __construct(
        public string $stimulusController='@survos/pwa-extra-bundle/detector')
    {

    }

    #[PreMount()]
    public function mount()
    {
        return ['stimulusController' => $this->stimulusController];

    }

}
