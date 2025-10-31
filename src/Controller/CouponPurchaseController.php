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

        // IRI ("/api/children/2") ou id ("2")
        $childId   = (int) basename((string)($data['child']   ?? ''));
        $subjectId = (int) basename((string)($data['subject'] ?? ''));
        $duration  = (int)($data['duration'] ?? 0);
        $quantity  = max(1, (int)($data['quantity'] ?? 1));

        /** @var Child|null $child */
        $child   = $em->getRepository(Child::class)->find($childId);
        /** @var Subject|null $subject */
        $subject = $em->getRepository(Subject::class)->find($subjectId);

        if (!$child || !$subject) {
            return $this->json(['message' => 'child/subject invalide'], 400);
        }
        if ($child->getParent() !== $this->getUser()) {
            return $this->json(['message' => 'Accès interdit'], 403);
        }
        if (!\in_array($duration, [60, 90, 120], true)) {
            return $this->json(['message' => 'Durée invalide (attendu: 60/90/120)'], 400);
        }

        // Grille tarifaire (Tariff.classLevel est une string ; Child.classLevel est un Enum)
        /** @var Tariff|null $tariff */
        $tariff = $em->getRepository(Tariff::class)->findOneBy([
            'classLevel'      => $child->getClassLevel()->value,
            'subject'         => $subject,
            'durationMinutes' => $duration,
            'isActive'        => true,
        ]);
        if (!$tariff) {
            return $this->json(['message' => 'Aucun tarif pour cette combinaison'], 422);
        }

        // Prix parent selon l’éligibilité au crédit d’impôt
        /** @var \App\Entity\User $parent */
        $parent = $this->getUser();
        $unitParentCents = $parent->isTaxCreditEligible()
            ? (int) $tariff->getPriceCentsAfterCredit()
            : (int) $tariff->getPriceCentsBeforeCredit();

        // Prix prof : obligatoire et > 0
        $teacherRate = (int) ($tariff->getTeacherRateCents() ?? 0);
        if ($teacherRate <= 0) {
            return $this->json(['message' => 'Tarif professeur manquant pour cette combinaison'], 422);
        }

        $created = [];
        for ($i = 0; $i < $quantity; $i++) {
            // code unique stable
            $code = $gen->generate([
                $child->getId(),
                $subject->getId(),
                $child->getClassLevel()->value,
                (string) $duration,
                microtime(true) . $i, // un peu d’entropie
            ]);

            $coupon = (new Coupon())
                ->setCode($code)
                ->setChild($child)                     // ← un seul élève
                ->setSubject($subject)                 // ← une seule matière
                ->setClassLevel($child->getClassLevel())
                ->setDurationMinutes($duration)
                ->setRemainingMinutes($duration)
                ->setStatus(CouponStatus::NEW)
                ->setPurchasedAt(new \DateTimeImmutable());

            // ⚠️ Si tu as ajouté des champs snapshot dans Coupon, décommente :
            // $coupon->setUnitPriceParentCents($unitParentCents);
            // $coupon->setUnitPriceTeacherCents($teacherRate);

            $em->persist($coupon);

            $created[] = [
                'code'       => $code,
                'duration'   => $duration,
                'priceCents' => $unitParentCents,
            ];
        }

        $em->flush();

        return $this->json([
            'child'        => $child->getId(),
            'subject'      => $subject->getId(),
            'quantity'     => $quantity,
            'unitPrice'    => $unitParentCents,
            'totalCents'   => $unitParentCents * $quantity,
            'taxEligible'  => $parent->isTaxCreditEligible(),
            'teacherRate'  => $teacherRate,
            'coupons'      => $created,
        ], 201);
    }
}