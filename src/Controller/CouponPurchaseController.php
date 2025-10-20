<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Entity\Child;
use App\Entity\Subject;
use App\Entity\Tariff;
use App\Enum\CouponStatus;
use App\Service\CouponCodeGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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

        $childId   = (int) basename((string)($data['child']   ?? ''));
        $subjectId = (int) basename((string)($data['subject'] ?? ''));
        $duration  = (int)($data['duration'] ?? 0);
        $quantity  = max(1, (int)($data['quantity'] ?? 1));

        $child   = $em->getRepository(Child::class)->find($childId);
        $subject = $em->getRepository(Subject::class)->find($subjectId);

        $parent = $this->getUser();

        $unit = $parent->isTaxCreditEligible()
            ? $tariff->getPriceCentsAfterCredit()
            : $tariff->getPriceCentsBeforeCredit();

        if (!$child || !$subject) {
            return $this->json(['message' => 'child/subject invalide'], 400);
        }
        if ($child->getParent() !== $this->getUser()) {
            return $this->json(['message' => 'Accès interdit'], 403);
        }
        if (!in_array($duration, [60, 90, 120], true)) {
            return $this->json(['message' => 'Durée invalide'], 400);
        }

        $tariff = $em->getRepository(Tariff::class)->findOneBy([
            'classLevel'      => $child->getClassLevel(),
            'subject'         => $subject,
            'durationMinutes' => $duration,
            'isActive'        => true,
        ]);
        if (!$tariff) {
            return $this->json(['message' => 'Aucun tarif pour cette combinaison'], 422);
        }

        $created = [];
        for ($i = 0; $i < $quantity; $i++) {
            $code = $gen->generate([
                $child->getId(),
                $subject->getId(),
                $child->getClassLevel()->value,
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
                'priceCents' => $tariff->getPriceCents(),
            ];
        }

        $em->flush();

        return $this->json([
            'child'      => $child->getId(),
            'subject'    => $subject->getId(),
            'quantity'   => $quantity,
            'unitPrice'  => $tariff->getPriceCents(),
            'totalCents' => $tariff->getPriceCents() * $quantity,
            'coupons'    => $created,
        ], 201);
    }
}