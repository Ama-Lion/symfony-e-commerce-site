<?php

namespace App\Controller;

use App\Cart\CartService;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, CartService $cartService, Request $request)
    {

        // 0.  find product and check if its exist
        $product = $productRepository->find($id);

        if (!$product) {
            throw $this->createNotFoundException("The product $id does not exist");
        }

        $cartService->add($id);

        $this->addFlash('success', 'Product Added Successfully');
        
        if($request->query->get('returnToCart')){
            return $this->redirectToRoute('cart_show');
        }

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

    /**
     * @Route("/cart/delete/{id}", name="cart_delete")
     */
    public function delete($id, ProductRepository $productRepository, CartService $cartService)
    {
        $product = $productRepository->find($id);
        if(!$product){
            throw $this->createNotFoundException("The product '$id' does not exist and can't not be deleted");
        }

        $cartService->remove($id);

        $this->addFlash('success', 'Product deleted successfully');

        return $this->redirectToRoute('cart_show');
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement")
     */
    public function decrement($id, CartService $cartService, ProductRepository $productRepository)
    {
        
        $product = $productRepository->find($id);
        if(!$product){
            throw $this->createNotFoundException("The product '$id' does not exist and can't not be deleted");
        }
        
        $cartService->decrement($id);
        
        $this->addFlash('success', 'Product Decremented successfully');

        return $this->redirectToRoute('cart_show');
    }
}
