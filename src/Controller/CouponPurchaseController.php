<?php

namespace App\Controller;

use App\Entity\Child;
use App\Entity\Coupon;
use App\Entity\Subject;
use App\Entity\Tariff;
use App\Enum\CouponStatus;
use App\Service\CouponCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/coupons')]
final class CouponPurchaseController extends AbstractController
{
    #[Route('/purchase', name: 'coupon_purchase', methods: ['POST'])]
    #[IsGranted('ROLE_PARENT')]
    public function purchase(
        Request $req,
        EntityManagerInterface $em,
        CouponCodeGenerator $gen
    ): JsonResponse {
        $data = json_decode($req->getContent(), true) ?: [];

        // on accepte IRIs (/api/children/2) ou ids "2"
        $childId   = (int) basename((string)($data['child']   ?? ''));
        $subjectId = (int) basename((string)($data['subject'] ?? ''));
        $duration  = (int)($data['duration'] ?? 0);      // 60, 90, 120
        $quantity  = max(1, (int)($data['quantity'] ?? 1));

        $child   = $em->getRepository(Child::class)->find($childId);
        $subject = $em->getRepository(Subject::class)->find($subjectId);

        if (!$child || !$subject) {
            return $this->json(['message' => 'child/subject invalide'], 400);
        }

        // sécurité : l’enfant doit appartenir au parent connecté
        if ($child->getParent() !== $this->getUser()) {
            return $this->json(['message' => 'Accès interdit'], 403);
        }

        if (!in_array($duration, [60, 90, 120], true)) {
            return $this->json(['message' => 'Durée invalide (attendu: 60/90/120)'], 400);
        }

        // Grille tarifaire : (classLevel, subject, duration, actif)
        /** @var Tariff|null $tariff */
        $tariff = $em->getRepository(Tariff::class)->findOneBy([
            'classLevel'      => $child->getClassLevel(),   // si enum: même type ici
            'subject'         => $subject,
            'durationMinutes' => $duration,
            'isActive'        => true,
        ]);

        if (!$tariff) {
            return $this->json(['message' => 'Aucun tarif pour cette combinaison'], 422);
        }

        // Prix unitaire selon l’éligibilité du parent au crédit d’impôt
        /** @var \App\Entity\User $parent */
        $parent = $this->getUser();

        $unitPriceCents = $parent->isTaxCreditEligible()
            ? (int) $tariff->getPriceCentsAfterCredit()
            : (int) $tariff->getPriceCentsBeforeCredit();

        $created = [];
        for ($i = 0; $i < $quantity; $i++) {
            // Génération d’un code stable et peu collisionnant
            $code = $gen->generate([
                $child->getId(),
                $subject->getId(),
                // si enum : ->value ; si string : cast
                method_exists($child->getClassLevel(), 'value')
                    ? $child->getClassLevel()->value
                    : (string) $child->getClassLevel(),
                (string) $duration,
            ]);

            $coupon = (new Coupon())
                ->setCode($code)
                ->setChild($child)
                ->setSubject($subject)
                ->setClassLevel($child->getClassLevel())
                ->setDurationMinutes($duration)
                ->setRemainingMinutes($duration)
                ->setStatus(CouponStatus::NEW)
                ->setPurchasedAt(new \DateTimeImmutable());

            $em->persist($coupon);

            $created[] = [
                'code'       => $code,
                'duration'   => $duration,
                'priceCents' => $unitPriceCents,
            ];
        }

        $em->flush();

        return $this->json([
            'child'        => $child->getId(),
            'subject'      => $subject->getId(),
            'quantity'     => $quantity,
            'unitPrice'    => $unitPriceCents,
            'totalCents'   => $unitPriceCents * $quantity,
            'taxEligible'  => $parent->isTaxCreditEligible(),
            'coupons'      => $created,
        ], 201);
    }
}