<?php

namespace App\Controller;

use App\Entity\CartPosition;
use Flasher\Notyf\Prime\NotyfFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


use App\Entity\Product;
use App\Entity\Category;

class ProductController extends AbstractController
{

    /**
     * @Route("/", name="app_products")
     */
    public function index(Request $request): Response{
        return $this->getProducts($request);
    }

    /**
     * @Route("/products", name="get_products")
     */
    public function getProducts(Request $request): Response
    {
        $id = $request->query->get('category');
        $bestProducts = null;

        $repository = $this->getDoctrine()->getRepository(Product::class);
        $cartRepo = $this->getDoctrine()->getRepository(CartPosition::class);
        $categoryRepo =  $this->getDoctrine()->getRepository(Category::class);

        $categories = $categoryRepo->findAll();


        $positions = $cartRepo->findBest($id);
        foreach ($positions as $position) {
            $bestProducts[] = $repository->find($position['id']);
        }

        $products = $repository->findByCategoryId($id);


        return $this->render('product/index.html.twig', [
            'products' => $products,
            'bestProducts' => $bestProducts,
            'categories' => $categories,
            'categoryId' => $id
        ]);
    }

    /**
     * @Route("/add-product", name="create_product")
     * @IsGranted("ROLE_ADMIN")
     */
    public function createProduct(Request $request, NotyfFactory $flasher): Response
    {
        $error = null;
        $name = "";
        $description = "";
        $price = 0;
        $category = null;

        $repo =  $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findAll();

        if($request->getMethod() == Request::METHOD_POST){

            $name = $request->request->get("name");
            $description = $request->request->get("description");
            $price = $request->request->get("price");
            $category = $request->request->get("category");

            if (trim($name, " ") == "" || trim($description, " ") == "" || $price <= 0) {
                $error = "Please fill the necessary fields!";
            } else {
                $entityManager = $this->getDoctrine()->getManager();

                $cat = $repo->find($category);
                $product = new Product($name, $price, $description, $cat);
                $product->setAvailable(true);

                $entityManager->persist($product);
                $entityManager->flush();

                $flasher->addSuccess('Product has been added successfully!');
                $name = "";
                $description = "";
                $price = 0;
            }
        }

        return $this->render('product/add.html.twig', [
            'error' => $error,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'categoryId' => $category,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/edit-product/{id}", name="edit_product", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editProduct(Request $request, Product $product, NotyfFactory $flasher): Response
    {
        $error = null;
        $name = "";
        $description = "";
        $price = 0;
        $categoryId = null;

        $repo =  $this->getDoctrine()->getRepository(Category::class);
        $categories = $repo->findAll();

        if($request->getMethod() == Request::METHOD_GET){

            $name = $product->getName();
            $description = $product->getDescription();
            $price = $product->getPrice();
            $categoryId = $product->getCategory()->getId();

        }else if($request->getMethod() == Request::METHOD_POST){

            $name = $request->request->get("name");
            $description = $request->request->get("description");
            $price = $request->request->get("price");
            $categoryId = $request->request->get("category");

            if (trim($name, " ") == "" || trim($description, " ") == "" || $price <= 0) {
                $error = "Please fill the necessary fields!";
            } else {

                $entityManager = $this->getDoctrine()->getManager();

                $category = $repo->find($categoryId);

                $product->setName($name);
                $product->setDescription($description);
                $product->setPrice($price);
                $product->setCategory($category);
                $entityManager->flush();

                $flasher->addSuccess('Data has been saved successfully!');
                return $this->redirectToRoute('app_products');
            }
        }

        return $this->render('product/edit.html.twig', [
            'error' => $error,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'categories' => $categories,
            'categoryId' => $categoryId
        ]);
    }

    /**
     * @Route("/delete-product/{id}", name="delete_product", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteProduct(Request $request, Product $product, NotyfFactory $flasher): Response{

        if($request->getMethod() == Request::METHOD_GET) {

            return $this->render('product/delete.html.twig', [
                'product' => $product
            ]);

        }else{

            $entityManager = $this->getDoctrine()->getManager();
            $product->setAvailable(false);
            $entityManager->flush();

            $flasher->addSuccess('Product has been deleted successfully!');
            return $this->redirectToRoute('app_products');
        }
    }

    /**
     * @Route("/product-details/{id}", name="product_details", requirements={"id": "\d+"})
     * @IsGranted("ROLE_USER")
     */
    public function productDetails(Product $product): Response
    {
        return $this->render('product/details.html.twig', [
            'product' => $product
        ]);
    }

}
