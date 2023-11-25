<?php
namespace App\Controller;

use App\Form\Tests\UploadType;
use App\Service\Uploader\Uploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/test', name: 'app_test_')]
class TestController extends AbstractController {

    public function __construct(
        private Uploader $uploader
    ) {
    }


    #[Route(path: '/upload', name: 'upload', methods: ['GET', 'POST'])]
    public function index(Request $request): Response {
        $form = $this->createForm(UploadType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $response = $this->uploader->upload($form->get('file')->getData(), fileType: 'image');
            dd($response);
        }

        return $this->render("tests/uploads.html.twig", compact('form'));
    }
}