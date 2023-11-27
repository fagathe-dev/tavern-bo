<?php
namespace App\Controller;

use App\Form\Arc\ImportType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/arc", name: "app_arc_")]
final class ArcController extends AbstractController {

    public function __construct() {

    }

    #[Route(path: '/import', name: 'import', methods: ['GET', 'POST'])]
    public function import(Request $request): Response {

        
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
        }

        return $this->render("arc/import.html.twig", compact('form'));
    }

}
