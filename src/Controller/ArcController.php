<?php
namespace App\Controller;

use App\Entity\Arc;
use App\Form\Arc\ArcType;
use App\Form\Arc\ImportType;
use App\Service\ArcService;
use App\Service\Breadcrumb\Breadcrumb;
use App\Service\Breadcrumb\BreadcrumbItem;
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

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(Arc $arc, Request $request): Response {
        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des arcs', $this->generateUrl('app_arc_index')),
            new BreadcrumbItem('Modifier un arc'),
        ]);

        $form = $this->createForm(ArcType::class, $arc);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $this->service->edit($form, $arc);
            if($result === true){
                return $this->redirectToRoute('app_arc_edit', ['id'=> $arc->getId()]);
            }
        }
        return $this->render('arc/edit.html.twig', compact('form', 'arc', 'breadcrumb'));
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response {
        $arc = new Arc;
        $form = $this->createForm(ArcType::class, $arc);
        $form->handleRequest($request);

        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des arcs', $this->generateUrl('app_arc_index')),
            new BreadcrumbItem('Ajouter un arc'),
        ]);

        if($form->isSubmitted() && $form->isValid()) {
            $upload = $this->service->saveImage($form, $arc);
            if($upload instanceof Arc && $this->service->create($arc)) {
                return $this->redirectToRoute('app_arc_index');
            }
        }

        return $this->render('arc/create.html.twig', compact('form', 'breadcrumb'));
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
