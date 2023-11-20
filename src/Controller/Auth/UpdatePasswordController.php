<?php
namespace App\Controller\Auth;

use App\Form\User\ChangePasswordType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: "/auth/update-password", name: "auth_update_password")]
class UpdatePasswordController extends AbstractController
{
    public function __construct(private UserService $userService)
    {
    }

    #[Route(path:"", name:"", methods: ['GET', 'POST'])]
    public function index(): Response
    {
        $form = $this->createForm(ChangePasswordType::class);
        return $this->render("auth/account/change-password.html.twig", compact("form"));
    }
}