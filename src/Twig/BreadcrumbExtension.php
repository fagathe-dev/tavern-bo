<?php
namespace App\Twig;

use App\Service\Breadcrumb\Breadcrumb;
use App\Service\Breadcrumb\BreadcrumbGenerator;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

final class BreadcrumbExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('generate_breadcrumb', [$this, 'generateBreadcrumb'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * generateBreadcrumb
     *
     * @param  mixed $breadcrumb
     * @return string
     */
    public function generateBreadcrumb(?Breadcrumb $breadcrumb): string
    {
        return (new BreadcrumbGenerator($breadcrumb))->generate();
    }
}