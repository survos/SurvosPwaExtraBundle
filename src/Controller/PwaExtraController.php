<?php

namespace Survos\PwaExtraBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Attribute\Route;
use Zenstruck\Console\CommandRunner;
use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Messenger\MessageBusInterface;

class PwaExtraController extends AbstractController
{


    public function __construct(
                                private array $config=[])
    {
    }

    #[Route('/icon/{size}', name: 'command_list')]
    public function icon($size=512): Response
    {
        return $this->redirectToRoute('app_homepage');
    }

}
