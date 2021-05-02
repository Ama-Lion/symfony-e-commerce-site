<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasePaymentSuccessController extends AbstractController
{
    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     */
    public function success($id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        $purchase = $purchaseRepository->find($id);

        if (
            !$purchase || 
            ($purchase && $purchase->getUser() !== $this->getUser()) ||
             ($purchase && $purchase->getStatus() === Purchase::STATUS_PAID)
        ) 
        {
            $this->addFlash('warning', 'The order does not exist');
            return $this->redirectToRoute('purchases_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();

        $cartService->emptyCart();

        $this->addFlash('success', 'Purchase Ordered successfully');
        return $this->redirectToRoute('purchases_index');
    }
}
