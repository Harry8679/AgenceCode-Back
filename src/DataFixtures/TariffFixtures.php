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
            // 6 eme
            ['classLevel' => '6e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            // 5 eme
            ['classLevel' => '5e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4700, 'after' => 2300, 'active' => true],
            ['classLevel' => '5e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4700, 'after' => 2300, 'active' => true],
            ['classLevel' => '5e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4700, 'after' => 2300, 'active' => true],
            ['classLevel' => '5e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4700, 'after' => 2300, 'active' => true],
            // 4 eme
            ['classLevel' => '4e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4800, 'after' => 2400, 'active' => true],
            ['classLevel' => '4e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4800, 'after' => 2400, 'active' => true],
            ['classLevel' => '4e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4800, 'after' => 2400, 'active' => true],
            ['classLevel' => '4e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4800, 'after' => 2400, 'active' => true],
            // 3 eme
            ['classLevel' => '3e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4900, 'after' => 2500, 'active' => true],
            ['classLevel' => '3e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4900, 'after' => 2500, 'active' => true],
            ['classLevel' => '3e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4900, 'after' => 2500, 'active' => true],
            ['classLevel' => '3e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4900, 'after' => 2500, 'active' => true],
            // 2nd
            ['classLevel' => '2nde', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5100, 'after' => 2600, 'active' => true],
            ['classLevel' => '2nde', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5100, 'after' => 2600, 'active' => true],
            ['classLevel' => '2nde', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5100, 'after' => 2600, 'active' => true],
            ['classLevel' => '2nde', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5100, 'after' => 2600, 'active' => true],
            // 1 ere
            ['classLevel' => '1ère', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5200, 'after' => 2700, 'active' => true],
            ['classLevel' => '1ère', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5200, 'after' => 2700, 'active' => true],
            ['classLevel' => '1ère', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5200, 'after' => 2700, 'active' => true],
            ['classLevel' => '1ère', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5200, 'after' => 2700, 'active' => true],
            // Terminale
            ['classLevel' => 'Terminale', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5300, 'after' => 2800, 'active' => true],
            ['classLevel' => 'Terminale', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5300, 'after' => 2800, 'active' => true],
            ['classLevel' => 'Terminale', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5300, 'after' => 2800, 'active' => true],
            ['classLevel' => 'Terminale', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5300, 'after' => 2800, 'active' => true],
            // License 1
            ['classLevel' => 'Bac+1', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5400, 'after' => 2900, 'active' => true],
            ['classLevel' => 'Bac+1', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5400, 'after' => 2900, 'active' => true],
            ['classLevel' => 'Bac+1', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5400, 'after' => 2900, 'active' => true],
            ['classLevel' => 'Bac+1', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5400, 'after' => 2900, 'active' => true],
            // License 2
            ['classLevel' => 'Bac+2', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5500, 'after' => 3000, 'active' => true],
            ['classLevel' => 'Bac+2', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5500, 'after' => 3000, 'active' => true],
            ['classLevel' => 'Bac+2', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5500, 'after' => 3000, 'active' => true],
            ['classLevel' => 'Bac+2', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5500, 'after' => 3000, 'active' => true],
            // License 3
            ['classLevel' => 'Bac+3', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 5600, 'after' => 3100, 'active' => true],
            ['classLevel' => 'Bac+3', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 5600, 'after' => 3100, 'active' => true],
            ['classLevel' => 'Bac+3', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 5600, 'after' => 3100, 'active' => true],
            ['classLevel' => 'Bac+3', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 5600, 'after' => 3100, 'active' => true],
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