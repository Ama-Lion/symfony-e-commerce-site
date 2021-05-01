<?php
namespace App\Controller\Purchase;

use App\Entity\User;
use Twig\Environment;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController 
{

    protected $security;

    public function __construct(Security $security)
    {

        $this->security = $security;

    }
    
    /**
     *@Route("/profile", name="Purchases_index")
     *
     */
    public function index()
    {
        /** @var User */
        $user = $this->security->getUser();

        if(!$user){
            throw new AccessDeniedException('You are not allowed to view this page');
        }

        return $this->render('profile/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}