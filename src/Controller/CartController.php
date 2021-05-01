<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService)
    {

        // 0.  find product and check if its exist
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("The product $id does not exist");
        }

        $cartService->add($id);

        $this->addFlash('success', 'Product Added Successfully');

        return $this->redirectToRoute('show_product', [
            'slug' => $product->getSlug()
        ]);
    }

    /**
     * @Route("/cart", name="cart_show")
     */
    public function show(CartService $cartService)
    {
        $detailedCart = $cartService->getDetailedItems();
        $total = $cartService->getTotal() ;
        
        return $this->render('cart/index.html.twig', [
            'items' => $detailedCart,
            'total' => $total
        ]);
    }
}
