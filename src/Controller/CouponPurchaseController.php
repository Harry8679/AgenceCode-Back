<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CouponPurchaseController extends AbstractController
{
    #[Route('/coupon/purchase', name: 'app_coupon_purchase')]
    public function index(): Response
    {
        return $this->render('coupon_purchase/index.html.twig', [
            'controller_name' => 'CouponPurchaseController',
        ]);
    }
}
