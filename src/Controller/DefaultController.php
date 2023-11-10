<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DefaultController extends AbstractController
{

    #[Route(path: "/", name: "app_default", methods: ["GET"])]
    public function default(): Response
    {
        return $this->render("default/index.html.twig");
    }
}