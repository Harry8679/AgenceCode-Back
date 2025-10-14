<?php

// src/DataFixtures/AdminUserFixtures.php
use App\Entity\User;
use App\Enum\UserProfile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AdminUserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher) {}

    public function load(ObjectManager $em): void
    {
        $u = (new User())
            ->setEmail('xavi.lamachine@gmail.com')
            ->setFirstName('Xavi')
            ->setLastName('La Machine')
            ->setProfile(UserProfile::ADMIN)        // 👈
            ->setRoles(['ROLE_ADMIN','ROLE_USER']); // optionnel

        $u->setPassword($this->hasher->hashPassword($u, 'Azerty123'));

        $em->persist($u);
        $em->flush();
    }
}