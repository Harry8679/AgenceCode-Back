<?php

namespace App\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

final class RegisterController extends AbstractController
{
    #[Route('/api/v1/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        
        ): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/Auth/RegisterController.php',
        ]);
    }
}
