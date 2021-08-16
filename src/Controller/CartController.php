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
     * @Route("/cart", name="app_cart")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $userRepo = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepo->findOneByEmail($this->getUser()->getUserIdentifier());

        $repo = $this->getDoctrine()->getRepository(CartPosition::class);
        $cartPositions = $repo->findByUserId($user->getId());

        $amount = 0;
        foreach($cartPositions as $cartPosition){
            $amount += $cartPosition->getProduct()->getPrice() * $cartPosition->getQuantity();
        }

        return $this->render('cart/index.html.twig', [
            'cartPositions' => $cartPositions,
            'amount' => $amount,
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

    /**
     * @Route("/edit-cart-product/{id}", name="edit_cart_product")
     * @IsGranted("ROLE_USER")
     */
    public function editCartProduct(Request $request, CartPosition $cartPosition, NotyfFactory $flasher): Response
    {
        $error = null;
        $quantity = $cartPosition->getQuantity();

        if($request->getMethod() == Request::METHOD_POST){

            $quantity = $request->request->get('quantity');

            if ($quantity <= 0) {
                $error = "The quantity must be greater than 0.";
            } else {

                $entityManager = $this->getDoctrine()->getManager();
                $cartPosition->setQuantity($quantity);
                $entityManager->flush();

                $flasher->addSuccess('Data has been saved successfully!');
                return $this->redirectToRoute('app_cart');
            }
        }

        return $this->render('cart/edit.html.twig', [
            'error' => $error,
            'product' => $cartPosition->getProduct(),
            'quantity' => $quantity
        ]);
    }

    /**
     * @Route("/remove-product/{id}", name="remove_product_from_cart", requirements={"id": "\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function removeProduct(Request $request, CartPosition $cartPosition, NotyfFactory $flasher): Response{

        if($request->getMethod() == Request::METHOD_GET) {

            return $this->render('cart/delete.html.twig', [
                'product' => $cartPosition->getProduct(),
                'quantity' => $cartPosition->getQuantity()
            ]);

        }else{

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($cartPosition);
            $entityManager->flush();

            $flasher->addSuccess('Data has been deleted successfully!');
            return $this->redirectToRoute('app_cart');
        }
    }
}
