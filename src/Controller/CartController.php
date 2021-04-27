<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, ProductRepository $productRepository, SessionInterface $session)
    {

      // 0.  find product and check if its exist
      $product = $productRepository->find($id);
      if (!$product){
          throw $this->createNotFoundException("The product $id does not exist");
      }
      // 1. find the product in the local storage  array 
      // 2. if it does not exist create a new array 
      $cart = $session->get('cart', []);

      // 3.   check if the product id in the table      
      // 4. else just add a new one with quantity one and quantity
      if(array_key_exists($id, $cart)){
          $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }
        
        // 5. save the table updated in the session
       $session->set('cart', $cart);

        $session->remove('cart');
        // dd($request->getSession()->get('cart'));

        return $this->redirectToRoute('show_product', [
            'slug' => $product->getSlug()
        ]);
    }
}
