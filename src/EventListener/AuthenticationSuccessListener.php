<?php
// src/EventListener/AuthenticationSuccessListener.php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User; // Assurez-vous que le chemin vers votre entité User est correct

class AuthenticationSuccessListener
{
    /**
     * @param AuthenticationSuccessEvent $event
     */
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        // On récupère les données de la réponse actuelle (qui ne contient que le token)
        $data = $event->getData();
        
        // On récupère l'objet User qui vient d'être authentifié
        $user = $event->getUser();

        // Si l'utilisateur n'est pas une instance de votre classe User, on ne fait rien
        if (!$user instanceof User) {
            return;
        }

        // On ajoute les données de l'utilisateur au tableau de la réponse.
        // Assurez-vous que les méthodes comme getFirstName(), getProfile() etc. existent bien dans votre entité User.
        $data['user'] = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $user->getFirstName(),
            'lastName' => $user->getLastName(),
            'profile' => $user->getProfile(),
            'roles' => $user->getRoles(),
        ];

        // On met à jour la réponse avec le nouveau tableau de données (token + user)
        $event->setData($data);
    }
}