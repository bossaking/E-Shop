<?php

namespace App\Controller;

use Flasher\Notyf\Prime\NotyfFactory;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Entity\Category;

class CategoryController extends AbstractController
{
    /**
     * @Route("/categories", name="app_categories")
     * @IsGranted("ROLE_ADMIN")
     */
    public function index(): Response
    {
//        if($this->getUser() == null || !in_array('ROLE_ADMIN', $this->getUser()->getRoles())){
//            return $this->redirectToRoute('app_login');
//        }

        $repository = $this->getDoctrine()->getRepository(Category::class);

        $categories = $repository->findAll();

        return $this->render('category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/add-category", name="add_category")
     * @IsGranted("ROLE_ADMIN")
     */
    public function addCategory(Request $request, NotyfFactory $flasher): Response
    {

        $name = "";
        $description = "";
        $error = null;

        if($request->getMethod() == Request::METHOD_POST){
            $name = $request->request->get("name");
            $description = $request->request->get("description");

            if (trim($name, " ") == "" || trim($description, " ") == "") {
                $error = "Please fill the necessary fields!";
            } else {
                $entityManager = $this->getDoctrine()->getManager();

                $category = new Category();
                $category->setName($name);
                $category->setDescription($description);

                $entityManager->persist($category);
                $entityManager->flush();

                $flasher->addSuccess('Category has been added successfully!');
                $name = "";
                $description = "";
            }

        }

        return $this->render('category/add.html.twig', [
            'error' => $error,
            'name' => $name,
            'description' => $description
        ]);
    }

    /**
     * @Route("/edit-category/{id}", name="edit_category", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function editCategory(Request $request, Category $category, NotyfFactory $flasher): Response
    {
        $error = null;
        $name = "";
        $description = "";

        if($request->getMethod() == Request::METHOD_GET){
            $name = $category->getName();
            $description = $category->getDescription();
        }else if($request->getMethod() == Request::METHOD_POST){

            $name = $request->request->get("name");
            $description = $request->request->get("description");

            if (trim($name, " ") == "" || trim($description, " ") == "") {
                $error = "Please fill the necessary fields!";
            } else {

                $entityManager = $this->getDoctrine()->getManager();

                $category->setName($name);
                $category->setDescription($description);
                $entityManager->flush();

                $flasher->addSuccess('Data has been saved successfully!');
                return $this->redirectToRoute('app_categories');
            }
        }

        return $this->render('category/edit.html.twig', [
            'error' => $error,
            'name' => $name,
            'description' => $description
        ]);
    }

    /**
     * @Route("/delete-category/{id}", name="delete_category", requirements={"id": "\d+"})
     * @IsGranted("ROLE_ADMIN")
     */
    public function deleteCategory(Request $request, Category $category, NotyfFactory $flasher): Response{

        if($request->getMethod() == Request::METHOD_GET) {

            $name = $category->getName();
            $description = $category->getDescription();


            return $this->render('category/delete.html.twig', [
                'name' => $name,
                'description' => $description
            ]);

        }else{

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();

            $flasher->addSuccess('Data has been deleted successfully!');
            return $this->redirectToRoute('app_categories');
        }
    }

}
