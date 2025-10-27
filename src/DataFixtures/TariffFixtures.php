<?php

namespace App\DataFixtures;

use App\Entity\Tariff;
use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface; // ← ICI
use Doctrine\Persistence\ObjectManager;

class TariffFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['tariffs'];
    }

    public function load(ObjectManager $manager): void
    {
        $subjectRepo = $manager->getRepository(Subject::class);
        $tariffRepo  = $manager->getRepository(Tariff::class);

        $rows = [
            ['classLevel' => '6e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
        ];

        foreach ($rows as $r) {
            $subject = $subjectRepo->findOneBy(['name' => $r['subjectName']]);
            if (!$subject) {
                // Tu peux throw si tu préfères
                continue;
            }

            $existing = $tariffRepo->findOneBy([
                'classLevel'      => $r['classLevel'],
                'subject'         => $subject,
                'durationMinutes' => $r['duration'],
            ]);

            if ($existing) {
                $existing
                    ->setPriceCentsBeforeCredit($r['before'])
                    ->setPriceCentsAfterCredit($r['after'])
                    ->setIsActive($r['active']);
                continue;
            }

            (new Tariff())
                ->setClassLevel($r['classLevel'])
                ->setSubject($subject)
                ->setDurationMinutes($r['duration'])
                ->setPriceCentsBeforeCredit($r['before'])
                ->setPriceCentsAfterCredit($r['after'])
                ->setIsActive($r['active'])
            ;
            $manager->persist($subject); // pas nécessaire
            $manager->persist((new Tariff())
                ->setClassLevel($r['classLevel'])
                ->setSubject($subject)
                ->setDurationMinutes($r['duration'])
                ->setPriceCentsBeforeCredit($r['before'])
                ->setPriceCentsAfterCredit($r['after'])
                ->setIsActive($r['active'])
            );
        }

        $manager->flush();
    }
}