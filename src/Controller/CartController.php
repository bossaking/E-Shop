<?php

namespace App\Controller;

use App\Entity\CartPosition;
use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Flasher\Notyf\Prime\NotyfFactory;
use Flasher\Prime\Factory\NotificationFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(): Response
    {
        return $this->render('cart/index.html.twig', [
            'controller_name' => 'CartController',
        ]);
    }

    /**
     * @Route("/add-to-cart/{id}", name="add_to_cart")
     * @IsGranted("ROLE_USER")
     */
    public function addToCart(Request $request, Product $product, NotyfFactory $flasher): Response
    {
        $error = null;
        $quantity = 0;

        if($request->getMethod() == Request::METHOD_POST){

            $quantity = $request->request->get('quantity');

            if ($quantity <= 0) {
                $error = "The quantity must be greater than 0.";
            } else {

                $entityManager = $this->getDoctrine()->getManager();
                $repository = $this->getDoctrine()->getRepository(User::class);
                $user = $repository->findOneByEmail($this->getUser()->getUserIdentifier());

                $cartPosition = new CartPosition();
                $cartPosition->setProduct($product);
                $cartPosition->setQuantity($quantity);
                $cartPosition->setUser($user);

                $entityManager->persist($cartPosition);
                $entityManager->flush();

                $flasher->addSuccess('Product has been added to cart!');
                return $this->redirectToRoute('app_products');
            }
        }

        return $this->render('cart/add.html.twig', [
            'error' => $error,
            'product' => $product,
            'quantity' => $quantity
        ]);
    }
}
