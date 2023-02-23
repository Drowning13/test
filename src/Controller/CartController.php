<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Form\CartType;
use App\Form\ProductType;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    private CartRepository $repo;
    public function __construct(CartRepository $repo)
   {
      $this->repo = $repo;
   }
    /**
     * @Route("/", name="app_cart")
     */
    public function rajif(UserRepository $user, CartRepository $cartrepo): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $u = $user->getId();
        $cart = $cartrepo->findBy(['usercart'=>$user]);

        return $this->render('cart/index.html.twig', [
            'Cart'=>$cart
        ]);
    }

     /**
     * @Route("/{id}", name="cart_read",requirements={"id"="\d+"})
     */
    public function showAction(UserRepository $user, RepositoryFactory $repo, CartRepository $cartrepo): Response
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();
        $u = $user->getId();
        $cart = $cartrepo->findBy(['usercart'=>$user]);

        return $this->render('detail.html.twig', [
            'cart'=>$cart
        ]);
        
    }


/**
     * @Route("/delete/{id}",name="cart_delete",requirements={"id"="\d+"})
     */
    
     public function deleteAction(Request $request, Cart $p): Response
     {
         $this->repo->remove($p,true);
         return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
     }
 
    /**
     * @Route("/add/{id}", name="cart_create")
     */
    public function addCartAction(Product $product, ProductRepository $repo,
    CartRepository $cartrepo): Response
    {
        $user = $this->getUser();

            
        $cart = $cartrepo->findOneBy([
                'procart'=>$product,
                'usercart' =>$user]);
        //case 1: exist, product's status is true => error
        if($cart!=null){
            // return $this->render('');
            return new Response('error');
            return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);

        }
        //case 2: null -> addCart
        else{
            $cartObj = new Cart();
          $cartObj->setProcart($product);
          $cartObj->setUsercart($user);
          $cartrepo->add($cartObj,true);
          
           return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);

        }
    }

}


