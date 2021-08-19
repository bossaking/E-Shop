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
        $statuses = null;
        $id = $request->query->get('status');
        $ordersRepo = $this->getDoctrine()->getRepository(Order::class);

        if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())){

            if($id != null && $id != 0){
                $orders = $ordersRepo->findByStatusId($id);
            }else{
                $orders = $ordersRepo->findAll();
            }
            $repo =  $this->getDoctrine()->getRepository(Status::class);
            $statuses = $repo->findAll();

        }else{
            $user = $this->getActualUser();
            $orders = $ordersRepo->findByUserId($user->getId());
        }

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
     * @Route("/order-details/{id}", name="order_details")
     * @IsGranted("ROLE_USER")
     */
    public function orderDetails(Request $request, Order $order, NotyfFactory $flasher): Response
    {

        if($request->getMethod() == Request::METHOD_GET){

            $repo =  $this->getDoctrine()->getRepository(Status::class);
            $statuses = $repo->findAll();
            $amount = 0;

            foreach ($order -> getCartPositions() as $cartPosition){
                $amount += $cartPosition->getProduct()->getPrice() * $cartPosition->getQuantity();
            }

            return $this->render('orders/details.html.twig', [
                'order' => $order,
                'statuses' => $statuses,
                'amount' => $amount,
                'statusId' => $order->getStatus()->getId()
            ]);
        }else{

            $entityManager = $this->getDoctrine()->getManager();
            $statusRepo = $this->getDoctrine()->getRepository(Status::class);

            if(in_array("ROLE_ADMIN", $this->getUser()->getRoles())){
                $statusId = $request->request->get('status');
                $status = $statusRepo->find($statusId);

                if($statusId == 3 || $statusId == 4) {
                    $order->setArchived(true);
                }else{
                    $order->setArchived(false);
                }
                $order->setStatus($status);

                $entityManager->flush();

                $flasher->addSuccess('Status has been changed successfully!');
            }else{
                $status = $statusRepo->find(4);

                $order->setArchived(true);
                $order->setStatus($status);
                $entityManager->flush();

                $flasher->addSuccess('Order has been removed successfully!');
            }

            return $this->redirectToRoute('app_orders');
        }
    }

    private function getActualUser(): User{
        $repository = $this->getDoctrine()->getRepository(User::class);
        return $repository->findOneByEmail($this->getUser()->getUserIdentifier());
    }
}
