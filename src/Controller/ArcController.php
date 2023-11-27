<?php
namespace App\Controller;

use App\Form\Arc\ImportType;
use App\Service\ArcService;
use App\Service\Breadcrumb\Breadcrumb;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/arc", name: "app_arc_")]
final class ArcController extends AbstractController {

    public function __construct(private ArcService $service) {
    }

    #[Route('', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response {

        return $this->render('arc/index.html.twig', $this->service->index($request));
    }

    #[Route(path: '/import', name: 'import', methods: ['GET', 'POST'])]
    public function import(Request $request): Response {
        $breadcrumb = new Breadcrumb();

        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->service->import($form);
        }

        return $this->render("arc/import.html.twig", compact('form', 'breadcrumb'));
    }

}
