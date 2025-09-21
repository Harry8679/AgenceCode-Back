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

class RegisterController extends AbstractController
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
        // 1) Désérialiser
        /** @var RegisterDto $dto */
        $dto = $serializer->deserialize($request->getContent(), RegisterDto::class, 'json');

        // 2) Valider DTO
        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            $errorsOut = [];
            foreach ($errors as $e) {
                $errorsOut[] = ['field' => $e->getPropertyPath(), 'message' => $e->getMessage()];
            }
            return $this->json(['message' => 'Invalid payload', 'errors' => $errorsOut], 422);
        }

        // 3) Créer l’utilisateur
        $user = (new User())
            ->setFirstName(trim($dto->firstName))
            ->setLastName(trim($dto->lastName))
            ->setEmail(strtolower(trim($dto->email)))
            ->setProfile(UserProfile::from($dto->profile));

        $user->setPassword($hasher->hashPassword($user, $dto->password));

        try {
            $em->persist($user);
            $em->flush();
        } catch (UniqueConstraintViolationException) {
            return $this->json(['message' => 'Email déjà utilisé.'], 409);
        }

        // 4) Générer un JWT pour login immédiat
        $token = $jwt->create($user);

        // 5) Réponse
        return $this->json([
            'id'        => $user->getId(),
            'firstName' => $user->getFirstName(),
            'lastName'  => $user->getLastName(),
            'email'     => $user->getEmail(),
            // 'profile'   => $user->getProfile()->value,
            'profile'   => $user->getProfile(),
            'roles'     => $user->getRoles(),
            'token'     => $token,
        ], 201);
    }
}
