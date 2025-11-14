<?php
// src/Controller/MyTeachersController.php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class MyTeachersController extends AbstractController
{
    #[Route('/api/my/teachers', methods: ['GET'])]
    #[IsGranted('ROLE_PARENT')]
    public function list(EntityManagerInterface $em): JsonResponse
    {
        /** @var User $parent */
        $parent = $this->getUser();

        $teachers = $em->createQueryBuilder()
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
            ->addOrderBy('t.firstName', 'ASC')
            ->getQuery()
            ->getResult();

        // On sérialise “à la main” les champs autorisés
        $data = array_map(fn(User $t) => [
            'id'         => $t->getId(),
            'firstName'  => $t->getFirstName(),
            'lastName'   => $t->getLastName(),
            'email'      => $t->getEmail(),
            'phone'      => $t->getPhoneNumber(),
        ], $teachers);

        return $this->json($data);
    }
}