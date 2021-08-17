<?php

namespace App\Controller;

use App\Entity\CartPosition;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\Status;
use App\Entity\User;
use App\Repository\StatusRepository;
use Flasher\Notyf\Prime\NotyfFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OrdersController extends AbstractController
{

    /**
     * @Route("/orders", name="app_orders")
     * @IsGranted("ROLE_USER")
     */
    public function index(Request $request): Response
    {
        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
            return $this->allOrders($request);
        }
        $user = $this->getActualUser();
        $ordersRepo = $this->getDoctrine()->getRepository(Order::class);
        $orders = $ordersRepo->findByUserId($user->getId());

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/all-orders", name="all_orders")
     * @IsGranted("ROLE_ADMIN")
     */
    public function allOrders(Request $request): Response
    {
        $ordersRepo = $this->getDoctrine()->getRepository(Order::class);

        $id = $request->query->get('status');
        if($id != null && $id != 0){
            $orders = $ordersRepo->findByStatusId($id);
        }else{
            $orders = $ordersRepo->findAll();
        }

        $repo =  $this->getDoctrine()->getRepository(Status::class);
        $statuses = $repo->findAll();

        return $this->render('orders/all-orders.html.twig', [
            'orders' => $orders,
            'statuses' => $statuses,
            'statusId' => $id
        ]);
    }

    /**
     * @Route("/orders-history", name="orders_history")
     * @IsGranted("ROLE_USER")
     */
    public function getOrdersHistory(): Response
    {
        $user = $this->getActualUser();
        $ordersRepo = $this->getDoctrine()->getRepository(Order::class);
        $orders = $ordersRepo->findByUserId($user->getId(), true);

        return $this->render('orders/history.html.twig', [
            'orders' => $orders,
        ]);
    }

    /**
     * @Route("/order-all", name="order_all")
     * @IsGranted("ROLE_USER")
     */
    public function orderAll(NotyfFactory $flasher): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = $this->getActualUser();

        $cartRepo = $this->getDoctrine()->getRepository(CartPosition::class);
        $cartPositions = $cartRepo->findByUserId($user->getId());

        $statusRepo = $this->getDoctrine()->getRepository(Status::class);
        $status = $statusRepo->find(1);

        $order = new Order();
        $order->setUser($user);
        $order->setStatus($status);

        foreach ($cartPositions as $cartPosition){
            $order->addCartPosition($cartPosition);
        }

        $entityManager->persist($order);
        $entityManager->flush();

        $flasher->addSuccess('The order has been successfully created!');
        return $this->redirectToRoute('app_orders');
    }

    /**
     * @Route("/remove-order/{id}", name="remove_order")
     * @IsGranted("ROLE_USER")
     */
    public function removeOrder(Request $request, Order $order, NotyfFactory $flasher): Response
    {

        $user = $this->getActualUser();

        if(!$user->getOrders()->contains($order)){
            return $this->redirectToRoute('app_orders');
        }

        if($request->getMethod() == Request::METHOD_GET){
            return $this->render('orders/delete.html.twig', [
                'order' => $order,
            ]);
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $statusRepo = $this->getDoctrine()->getRepository(Status::class);
            $status = $statusRepo->find(4);

            $order->setArchived(true);
            $order->setStatus($status);
            $entityManager->flush();

            $flasher->addSuccess('Data has been deleted successfully!');
            return $this->redirectToRoute('app_orders');
        }
    }

    /**
     * @Route("/edit-order/{id}", name="edit_order")
     * @IsGranted("ROLE_ADMIN")
     */
    public function editOrder(Request $request, Order $order, NotyfFactory $flasher): Response
    {

        if($request->getMethod() == Request::METHOD_GET){

            $repo =  $this->getDoctrine()->getRepository(Status::class);
            $statuses = $repo->findAll();

            return $this->render('orders/edit.html.twig', [
                'order' => $order,
                'statuses' => $statuses
            ]);
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $statusId = $request->request->get('status');
            $statusRepo = $this->getDoctrine()->getRepository(Status::class);
            $status = $statusRepo->find($statusId);

            if($statusId == 3 || $statusId == 4) {
                $order->setArchived(true);
            }else{
                $order->setArchived(false);
            }
            $order->setStatus($status);

            $entityManager->flush();

            $flasher->addSuccess('Status has been changed successfully!');
            return $this->redirectToRoute('app_orders');
        }
    }

    private function getActualUser(): User{
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $repository->findOneByEmail($this->getUser()->getUserIdentifier());
    }
}
