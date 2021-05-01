<?php
namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Routing\Annotation\Route;
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
     *@Route("/profile", name="purchases_index")
     *
     */
    public function index()
    {
        /** @var User */
        $user = $this->security->getUser();

        if(!$user){
            throw new AccessDeniedException('You are not allowed to view this page');
        }
        // dd($user->getPurchases());
        return $this->render('profile/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}