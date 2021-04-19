<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\User;
use App\Form\ProfileInfoFormType;
use App\Form\RegisterFormType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        $form = $this->createForm(RegisterFormType::class,$user);

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
     * @Route("/profile/info", name="profile_info", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function profileInfo(Request $request) : Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ProfileInfoFormType::class,$user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash("message","Utilisateur mise à jour");
            return $this->redirectToRoute('profile_info');
        }

        return $this->render('user/profileInfo.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/profile/article", name="profile_article", methods={"GET"})
     * @IsGranted ("ROLE_ADMIN")
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function profileArticle(ArticleRepository $articleRepository) : Response
    {
        $articles = $articleRepository->findAll();
        return $this->render('admin/profileArticle.html.twig',[
            'articles' => $articles,
        ]);
    }

}
