<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterFromType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    private $encoder;


    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */

    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFromType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $encoder->encodePassword($user,$user->getPassword())
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("succes","Utilisateur crée");

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile", name="profile", methods={"GET"})
     * @return Response
     */
    public function profile() : Response
    {
        return $this->render('user/profileHomepage.html.twig');
    }

    /**
     * @Route("/profile/info", name="profile_info", methods={"GET"})
     * @return Response
     */
    public function profileInfo() : Response
    {
        return $this->render('user/profileInfo.html.twig');
    }

}
