<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;

final class JWTAuthenticationSuccessListener
{
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();   // contient au moins ['token' => '...']
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return; // ex. login via autre provider
        }

        // ⚠️ Adapte ces getters aux champs de ton entity User
        $userData = [
            'id'         => method_exists($user, 'getId') ? $user->getId() : null,
            'email'      => method_exists($user, 'getEmail') ? $user->getEmail() : null,
            'firstName'  => method_exists($user, 'getFirstName') ? $user->getFirstName() : null,
            'lastName'   => method_exists($user, 'getLastName') ? $user->getLastName() : null,
            'profile'    => method_exists($user, 'getProfile')
                ? (is_object($user->getProfile()) && method_exists($user->getProfile(), 'value')
                    ? $user->getProfile()->value
                    : $user->getProfile())
                : null,
            'roles'      => $user->getRoles(),
        ];

        $event->setData([
            'token' => $data['token'],
            'user'  => $userData,
        ]);
    }
}