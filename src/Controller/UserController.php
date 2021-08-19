<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    /**
     * @Route("/add-user", name="create_user")
     */
    public function createUser(UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $plainPassword = 'root';
        $encoded = $encoder->encodePassword($user, $plainPassword);

        return new Response($encoded);
    }
}
