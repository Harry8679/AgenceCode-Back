<?php

namespace App\Controller\Auth;

use App\Dto\RegisterDto;
use App\Entity\User;
use App\Enum\UserProfile;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class RegisterController extends AbstractController
{
    #[Route('/api/v1/auth/register', name: 'api_auth_register', methods: ['POST'])]
    public function __invoke(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        JWTTokenManagerInterface $jwt
    ): JsonResponse {
        /** @var RegisterDto $dto */
        $dto = $serializer->deserialize($request->getContent(), RegisterDto::class, 'json');

        // 1) Validation du DTO
        $errors = $validator->validate($dto);
        if (\count($errors) > 0) {
            $out = [];
            foreach ($errors as $e) {
                $out[] = ['field' => $e->getPropertyPath(), 'message' => $e->getMessage()];
            }
            return $this->json(['message' => 'Invalid payload', 'errors' => $out], 422);
        }

        // 2) Normalisation et contrôle du profil
        //    - on accepte "parent|student|teacher|admin" insensible à la casse
        //    - on refuse ADMIN sur l’endpoint public
        try {
            $rawProfile = \strtoupper(\trim($dto->profile));
            $profile    = UserProfile::from($rawProfile);
        } catch (\ValueError) {
            return $this->json([
                'message' => 'Profil invalide. Valeurs autorisées: PARENT, STUDENT, TEACHER.'
            ], 422);
        }

        if ($profile === UserProfile::ADMIN) {
            // garde-fou contre l’élévation de privilèges à l’inscription publique
            return $this->json([
                'message' => 'Création d’un compte ADMIN interdite via l’inscription publique.'
            ], 403);
        }

        // 3) Création de l’utilisateur (les rôles seront dérivés du profil dans setProfile)
        $user = (new User())
            ->setFirstName(\trim($dto->firstName))
            ->setLastName(\trim($dto->lastName))
            ->setEmail(\strtolower(\trim($dto->email)))
            ->setProfile($profile);

        $user->setPassword($hasher->hashPassword($user, $dto->password));

        try {
            $em->persist($user);
            $em->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->json(['message' => 'Email déjà utilisé.'], 409);
        }

        // 4) JWT pour login immédiat
        $token = $jwt->create($user);

        // 5) Réponse
        return $this->json([
            'id'        => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName'  => $user->getLastName(),
            'email'     => $user->getEmail(),
            'profile'   => $user->getProfile()->value, // renvoie "PARENT|STUDENT|TEACHER"
            'roles'     => $user->getRoles(),
            'token'     => $token,
        ], 201);
    }
}