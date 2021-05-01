<?php

namespace App\Cart;

use App\Cart\CartItem;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService {

    protected $session;
    protected $productRepository;

    public function __construct(SessionInterface $session, ProductRepository $productRepository){
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public function add(int $id)
    {      
      // find the product in the local storage  array if it does not exist create a new array 
      $cart = $this->session->get('cart', []);
      
      //  else just add a new one with quantity one and quantity check if the product id in the table      
      if(array_key_exists($id, $cart)){
          $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }
        
        // 5. save the table updated in the session
       $this->session->set('cart', $cart);
    }


    public function getTotal(): int 
    {
        $total = 0;

        foreach ($this->session->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);

            if(!$product){
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    public function getDetailedItems(): array 
    {
        $detailedCart = [];

        foreach ($this->session->get('cart', []) as $id => $qty) {
            $product = $this->productRepository->find($id);
            
            if(!$product){
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        
        return $detailedCart;
    }
}