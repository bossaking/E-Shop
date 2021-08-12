<?php

namespace App\Controller;

use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="app_categories")
     */
    public function index(): Response
    {
        if($this->getUser() != null && in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)){
            return $this->render('category/index.html.twig', [
                'controller_name' => 'CategoryController',
            ]);
        }

        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/add-category", name="create_category")
     */
    public function createCategory(): Response
    {
        // you can fetch the EntityManager via $this->getDoctrine()
        // or you can add an argument to the action: createProduct(EntityManagerInterface $entityManager)
        $entityManager = $this->getDoctrine()->getManager();

        $category = new Category("Sport", "Sport");

        // tell Doctrine you want to (eventually) save the category (no queries yet)
        $entityManager->persist($category);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new category with id '.$category->getId());
    }
}
