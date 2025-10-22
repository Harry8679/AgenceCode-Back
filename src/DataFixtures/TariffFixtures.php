<?php

namespace App\DataFixtures;

use App\Entity\Tariff;
use App\Entity\Subject;
// ← dé-commente si tu utilises l'Enum côté Tariff
// use App\Enum\ClassLevel;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TariffFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        // Permet d’exécuter uniquement ce groupe si tu veux
        return ['tariffs'];
    }

    public function load(ObjectManager $manager): void
    {
        $subjectRepo = $manager->getRepository(Subject::class);
        $tariffRepo  = $manager->getRepository(Tariff::class);

        // Ta grille demandée (tu peux en ajouter d’autres lignes ici)
        $rows = [
            ['classLevel' => '6e', 'subjectName' => 'Mathématiques', 'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Physique',       'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Chimie',         'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
            ['classLevel' => '6e', 'subjectName' => 'Informatique',   'duration' => 60, 'before' => 4600, 'after' => 2200, 'active' => true],
        ];

        foreach ($rows as $r) {
            $subject = $subjectRepo->findOneBy(['name' => $r['subjectName']]);
            if (!$subject) {
                // Si la matière n'existe pas, on passe (ou lève une exception si tu préfères)
                // throw new \RuntimeException("Subject not found: {$r['subjectName']}");
                continue;
            }

            // Cherche s'il existe déjà un tarif identique (clé fonctionnelle)
            $existing = $tariffRepo->findOneBy([
                'classLevel'      => $r['classLevel'],
                'subject'         => $subject,
                'durationMinutes' => $r['duration'],
            ]);

            if ($existing) {
                // Mise à jour si déjà présent
                $existing
                    ->setPriceCentsBeforeCredit($r['before'])
                    ->setPriceCentsAfterCredit($r['after'])
                    ->setIsActive($r['active']);
                continue;
            }

            $tariff = new Tariff();

            // --- Si Tariff::$classLevel est une STRING ---
            $tariff->setClassLevel($r['classLevel']);

            // --- Si Tariff::$classLevel est un ENUM, utilise plutôt : ---
            // $tariff->setClassLevel(ClassLevel::SIXIEME);

            $tariff
                ->setSubject($subject)
                ->setDurationMinutes($r['duration'])
                ->setPriceCentsBeforeCredit($r['before'])
                ->setPriceCentsAfterCredit($r['after'])
                ->setIsActive($r['active']);

            $manager->persist($tariff);
        }

        $manager->flush();
    }
}