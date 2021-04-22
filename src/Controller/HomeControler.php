<?php

namespace App\Controller;

class HomeController extends AbstractController {
    public function homepage () {
        return $this->render('homepage.html.twig');
    }
}