<?php

namespace App\EventListener;

use App\Entity\User;                // ⬅️ ta classe User concrète
use App\Enum\UserProfile;           // ⬅️ si tu utilises une Enum pour le profil (sinon supprime)
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

final class JWTAuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();   // ['token' => '...']
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return; // sécurité
        }

        // Fallback par défaut (si ce n'est pas notre entité User)
        $userData = [
            'username' => $user->getUserIdentifier(),
            'roles'    => $user->getRoles(),
        ];

        // ✅ Cas attendu : c'est bien notre entité App\Entity\User
        if ($user instanceof User) {
            // ⚠️ ADAPTE les noms des getters ci-dessous à ton entity :
            // MakerBundle génère souvent getFirstname()/getLastname() (sans maj sur le N)
            $firstName = method_exists($user, 'getFirstName') ? $user->getFirstName()
                        : (method_exists($user, 'getFirstname') ? $user->getFirstname() : null);

            $lastName  = method_exists($user, 'getLastName') ? $user->getLastName()
                        : (method_exists($user, 'getLastname') ? $user->getLastname() : null);

            // Profil: Enum ou string
            $profile = null;
            if (method_exists($user, 'getProfile')) {
                $raw = $user->getProfile(); // peut être une Enum ou une string
                if ($raw instanceof UserProfile) {
                    $profile = $raw->value;
                } else {
                    $profile = $raw; // string
                }
            }

            $userData = [
                'id'        => method_exists($user, 'getId') ? $user->getId() : null,
                'email'     => method_exists($user, 'getEmail') ? $user->getEmail() : null,
                'firstName' => $firstName,
                'lastName'  => $lastName,
                'profile'   => $profile,
                'roles'     => $user->getRoles(),
            ];
        }

        $event->setData([
            'token' => $data['token'],
            'user'  => $userData,
        ]);
    }
}
