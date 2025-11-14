<?php
// src/Api/Provider/MyAssignmentsProvider.php
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

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): iterable
    {
        $user = $this->security->getUser();
        if (!$user) return [];

        // Exemple: renvoyer les affectations où le user est prof OU parent de l’enfant
        $qb = $this->em->getRepository(TeacherAssignment::class)->createQueryBuilder('a')
            ->leftJoin('a.teacher', 't')
            ->leftJoin('a.child', 'c')
            ->leftJoin('c.parent', 'p')
            ->andWhere('t = :u OR p = :u')
            ->setParameter('u', $user);

        return $qb->getQuery()->getResult();
    }
}