<?php
namespace App\Controller;

use App\Entity\User;
use App\Form\User\CreateType;
use App\Form\User\EditInfosType;
use App\Service\Breadcrumb\Breadcrumb;
use App\Service\Breadcrumb\BreadcrumbItem;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/user', name: 'app_user_')]
class UserController extends AbstractController
{
    public function __construct(private UserService $service)
    {
    }

    #[Route(path: '/edit/{id}', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function edit(User $user, Request $request): Response
    {
        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des utilisateurs', $this->generateUrl('app_user_index')),
            new BreadcrumbItem('Modifier l\'utilisateur #' . $user->getId()),
        ]);

        $form = $this->createForm(EditInfosType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->service->update($user)) {
                return $this->redirectToRoute('app_user_index');
            }
        }

        return $this->render('user/edit.html.twig', compact('form', 'user', 'breadcrumb'));
    }

    #[Route(path: '/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $breadcrumb = new Breadcrumb([
            new BreadcrumbItem('Liste des utilisateurs', $this->generateUrl('app_user_index')),
            new BreadcrumbItem('Ajouter un utilisateur'),
        ]);

        $user = new User;
        $form = $this->createForm(CreateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($this->service->create($user)) {
                return $this->redirectToRoute('app_user_index');
            }
        }

        return $this->render('user/create.html.twig', compact('form', 'user', 'breadcrumb'));
    }

    #[Route(path: '', name: 'index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render("user/index.html.twig", $this->service->index($request));
    }

    #[Route(path: '/delete/{id}', name: 'delete', methods: ['DELETE'], requirements: ['id' => '\d+'])]
    public function delete(User $user): JsonResponse
    {
        $response = $this->service->remove($user);
        if (gettype($response) === 'object') {
            return $this->json(
                $response->data,
                $response->status,
                $response->headers,
            );
        }

        return $this->json(
            'BAD REQUEST',
            Response::HTTP_BAD_REQUEST
        );
    }

}