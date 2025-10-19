<?php

// src/Api/Provider/MyCouponsProvider.php
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
        private Security $security
    ) {}

    // ✅ NOTE: le type de retour doit matcher l'interface
    // Selon ta version d’API Platform, c’est l’un des deux :
    //  - object|array|null   (v3.x)
    //  - iterable|object|null (certaines 3.x)
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $parent = $this->security->getUser();
        if (!$parent) {
            return []; // collection vide si non connecté
        }

        $qb = $this->em->getRepository(Coupon::class)->createQueryBuilder('c')
            ->join('c.child', 'ch')
            ->andWhere('ch.parent = :p')->setParameter('p', $parent)
            ->orderBy('c.purchasedAt', 'DESC');

        return $qb->getQuery()->getResult(); // ✅ retourne un array pour GetCollection
    }
}
