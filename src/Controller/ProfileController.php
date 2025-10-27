<?php

// src/Controller/ProfileController.php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/api/v1/me')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ProfileController extends AbstractController
{
    #[Route('', name: 'me_get', methods: ['GET'])]
    public function me(): JsonResponse
    {
        /** @var User $u */
        $u = $this->getUser();
        return $this->json([
            'id'        => $u->getId(),
            'email'     => $u->getEmail(),
            'firstName' => $u->getFirstName(),
            'lastName'  => $u->getLastName(),
            'phone'     => method_exists($u, 'getPhone') ? $u->getPhoneNumber() : null,
            'profile'   => method_exists($u, 'getProfile') ? $u->getProfile() : null,
            'roles'     => $u->getRoles(),
        ]);
    }

    #[Route('', name: 'me_patch', methods: ['PATCH'])]
    public function update(Request $req, EntityManagerInterface $em): JsonResponse
    {
        /** @var User $u */
        $u = $this->getUser();

        $data = json_decode($req->getContent(), true) ?? [];
        if (isset($data['firstName'])) $u->setFirstName(trim((string)$data['firstName']));
        if (isset($data['lastName']))  $u->setLastName(trim((string)$data['lastName']));
        if (isset($data['phone']) && method_exists($u, 'setPhone')) $u->setPhone(trim((string)$data['phone']));

        $em->flush();
        return $this->json(['message' => 'ok']);
    }

    #[Route('/password', name: 'me_password', methods: ['POST'])]
    public function changePassword(
        Request $req,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): JsonResponse {
        /** @var User $u */
        $u = $this->getUser();

        $data = json_decode($req->getContent(), true) ?? [];
        $current = (string)($data['currentPassword'] ?? '');
        $new     = (string)($data['newPassword'] ?? '');

        if ($current === '' || $new === '') {
            return $this->json(['message' => 'Champs requis'], 400);
        }
        if (!$hasher->isPasswordValid($u, $current)) {
            return $this->json(['message' => 'Mot de passe actuel invalide'], 400);
        }

        $u->setPassword($hasher->hashPassword($u, $new));
        $em->flush();

        return $this->json(['message' => 'ok']);
    }
}