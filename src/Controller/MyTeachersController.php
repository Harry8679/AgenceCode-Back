<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MyTeachersController extends AbstractController
{
    #[Route('/my/teachers', name: 'app_my_teachers')]
    public function index(): Response
    {
        return $this->render('my_teachers/index.html.twig', [
            'controller_name' => 'MyTeachersController',
        ]);
    }
}
