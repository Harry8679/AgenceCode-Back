<?php

namespace App\Api\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Coupon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MyCouponsProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {}

    // API Platform 3 : bonne signature
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $user = $this->security->getUser();
        if (!$user) return [];

        return $this->em->getRepository(Coupon::class)
            ->createQueryBuilder('c')
            ->join('c.child', 'ch')
            ->andWhere('ch.parent = :p')->setParameter('p', $user)
            ->orderBy('c.purchasedAt', 'DESC')
            ->getQuery()->getResult();
    }
}
