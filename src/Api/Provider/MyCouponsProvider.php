<?php

// src/Api/Provider/MyCouponsProvider.php
namespace App\Api\Provider;

use App\Entity\Coupon;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class MyCouponsProvider implements ProviderInterface
{
    public function __construct(private EntityManagerInterface $em, private Security $security) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        $parent = $this->security->getUser();
        if (!$parent) return [];

        return $this->em->getRepository(Coupon::class)
            ->createQueryBuilder('c')
            ->join('c.child','ch')
            ->andWhere('ch.parent = :p')->setParameter('p', $parent)
            ->orderBy('c.purchasedAt','DESC')
            ->getQuery()->getResult();
    }
}