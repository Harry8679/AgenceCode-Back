<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface as FixturesBundleFixtureGroupInterface;
use Doctrine\Common\DataFixtures\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AdminUserFixtures extends Fixture implements FixturesBundleFixtureGroupInterface
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $em): void
    {
        $email = 'xavi.lamachine@gmail.com';

        // Idempotent : si l'admin existe déjà, on le met à jour plutôt que de dupliquer
        $repo = $em->getRepository(User::class);
        $user = $repo->findOneBy(['email' => $email]) ?? new User();

        $user->setEmail($email)
             ->setFirstName('Xavi')
             ->setLastName('La Machine')
             ->setProfile(UserProfile::ADMIN); // setProfile pose aussi les rôles

        if (!$user->getId()) {
            $user->setPassword($this->hasher->hashPassword($user, 'Azerty123'));
            $em->persist($user);
        }

        $em->flush();
    }

    public static function getGroups(): array
    {
        return ['admin-seed']; // permet de lancer uniquement cette fixture
    }
}