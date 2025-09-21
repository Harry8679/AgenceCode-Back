<?php

// src/Controller/Auth/LoginController.php
namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/api/v1/auth/login', name: 'api_auth_login', methods: ['POST'])]
    public function __invoke(): JsonResponse
    {
        // Ne sera jamais exécuté si json_login est configuré.
        return $this->json(['message' => 'Handled by firewall'], 500);
    }
}
