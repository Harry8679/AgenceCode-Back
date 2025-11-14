<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Enum\UserProfile;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MyTeachersProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
        private EntityManagerInterface $em
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array
    {
        /** @var User $parent */
        $parent = $this->security->getUser();

        // Professeurs DISTINCT qui ont saisi au moins un CouponUsage pour un enfant du parent
        $qb = $this->em->createQueryBuilder()
            ->select('DISTINCT t')
            ->from(User::class, 't')
            ->innerJoin('t.couponUsages', 'cu')
            ->innerJoin('cu.coupon', 'co')
            ->innerJoin('co.child', 'ch')
            ->where('t.profile = :teacher')
            ->andWhere('ch.parent = :parent')
            ->setParameter('teacher', UserProfile::TEACHER)
            ->setParameter('parent', $parent)
            ->orderBy('t.lastName', 'ASC')
            ->addOrderBy('t.firstName', 'ASC');

        return $qb->getQuery()->getResult();
    }
}