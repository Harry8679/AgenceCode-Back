<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Child;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Provider pour retourner uniquement les enfants du parent connecté.
 */
final class MyChildrenProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();
        if (!$user) {
            return []; // ou throw AccessDeniedException
        }

        // On filtre par parent = user courant
        return $this->em->getRepository(Child::class)
            ->createQueryBuilder('c')
            ->andWhere('c.parent = :parent')
            ->setParameter('parent', $user)
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}