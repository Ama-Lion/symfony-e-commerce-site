<?php

namespace App\Stripe;

use App\Entity\Purchase;

class StripeService
{
    protected $secretkey;
    protected $publickey;

    public function __construct(string $secretkey, string $publickey)
    {
        $this->secretkey = $secretkey;
        $this->publickey = $publickey;
    }
    
    public function getPublicKey()
    {
        return $this->publickey;
    }
    
    public function getPaymentIntent(Purchase $purchase)
    {

     \Stripe\Stripe::setApikey($this->secretkey);

        return \Stripe\PaymentIntent::create([
            'amount' => $purchase->getTotal(),
            'currency' => 'eur'
        ]);
    }

}