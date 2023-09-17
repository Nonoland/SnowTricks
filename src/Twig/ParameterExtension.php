<?php

namespace App\Twig;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class ParameterExtension extends AbstractExtension
{
    protected ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('getSymfonyParameter', [$this, 'getSymfonyParameter'])
        ];
    }

    public function getSymfonyParameter($name): string
    {
        return $this->parameterBag->get($name);
    }
}