<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\TeacherAssignment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MyAssignmentsProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable|object|null
    {
        $user = $this->security->getUser();
        if (!$user) {
            return [];
        }

        $repo = $this->em->getRepository(TeacherAssignment::class);

        // Parent: on liste tout ce qui touche ses enfants
        if (in_array('ROLE_PARENT', $user->getRoles(), true)) {
            return $repo->createQueryBuilder('a')
                ->join('a.child', 'c')
                ->andWhere('c.parent = :u')
                ->setParameter('u', $user)
                ->getQuery()
                ->getResult();
        }

        // Teacher: on liste ce qui le concerne
        if (in_array('ROLE_TEACHER', $user->getRoles(), true)) {
            return $repo->findBy(['teacher' => $user]);
        }

        // Admin: tout
        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return $repo->findAll();
        }

        return [];
    }
}