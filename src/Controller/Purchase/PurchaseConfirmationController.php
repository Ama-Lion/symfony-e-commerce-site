<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchaseConfirmationController extends AbstractController
{
    protected $router;
    protected $security;
    protected $cartService;
    protected $em;

    public function __construct(
        Security $security,
        CartService $cartService,
        EntityManagerInterface $em
    ) {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Please fill the form to validate your purchase');
            return $this->redirectToRoute('cart_show');
        }

        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException('YOU NEED TO BE LOGGED IN TO CONFIRM A PURCHASE');
        }

        $cartItems = $this->cartService->getDetailedItems();
        if (count($cartItems) == 0) {
            $this->addFlash('warning', 'You can not confirm a purchase with empty products list');
            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase */
        $purchase = $form->getData();
        $purchase->setUser($user)
            ->setPurchasedAt(new DateTime());
        $total = 0;
        $this->em->persist($purchase);
        foreach ($this->cartService->getDetailedItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setQuantity($cartItem->qty)
                ->setTotal($cartItem->getTotal())
                ->setProductPrice($cartItem->product->getPrice())
                ->setProductImage($cartItem->product->getPicture());

            $total += $cartItem->getTotal();
            $this->em->persist($purchaseItem);
        }

        $purchase->setTotal($total);
        $this->em->flush();
        $this->addFlash('succes', 'Your Purchase was successfully processed');

        return $this->redirectToRoute('purchases_index');
    }
}
