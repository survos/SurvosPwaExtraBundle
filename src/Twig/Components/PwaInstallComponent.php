<?php

namespace Survos\PwaExtraBundle\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PreMount;

#[AsTwigComponent(name: 'PwaInstall', template:'@SurvosPwaExtra/components/pwa_install.html.twig')]
final class PwaInstallComponent
{
    public function __construct(
        public string $stimulusController='@survos/pwa-extra-bundle/install')
    {

    }

    #[PreMount()]
    public function mount()
    {
        return ['stimulusController' => $this->stimulusController];

    }

}
