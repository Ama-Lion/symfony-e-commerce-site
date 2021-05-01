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

    protected function getCart() : array
    {
        return $this->session->get('cart', []);
    }

    protected function saveCart(array $cart)
    {
        $this->session->set('cart', $cart);
    }

    public function emptyCart(){
        $this->saveCart([]);
    }

    public function add(int $id)
    {      
      // find the product in the local storage  array if it does not exist create a new array 
     $cart = $this->getCart();
      
      //  else just add a new one with quantity one and quantity check if the product id in the table      
      if(!array_key_exists($id, $cart)){
          $cart[$id] = 0 ;
        }

        $cart[$id]++;
        // 5. save the table updated in the session
       $this->saveCart($cart);
    }

    public function remove (int $id)
    {
       $cart = $this->getCart();

        unset($cart[$id]);

        $this->saveCart($cart);
    }

    public function decrement(int $id)
    {
       $cart = $this->getCart();

        if(!array_key_exists($id, $cart)){
            return;
        }

        if($cart[$id] == 1){
            $this->remove($id);
            return;
        }

        $cart[$id]--;

        $this->saveCart($cart);
    }

    public function getTotal(): int 
    {
        $total = 0;

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);

            if(!$product){
                continue;
            }

            $total += $product->getPrice() * $qty;
        }

        return $total;
    }

    /**
     * @return CartItem[]
     */
    public function getDetailedItems(): array 
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            
            if(!$product){
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        
        return $detailedCart;

    }

    public function getTotalItems () : int
    {
        $detailedCart = [];

        foreach ($this->getCart() as $id => $qty) {
            $product = $this->productRepository->find($id);
            
            if(!$product){
                continue;
            }

            $detailedCart[] = new CartItem($product, $qty);
        }
        
       $amount = count($detailedCart);
       
       return $amount;
    }
    
}