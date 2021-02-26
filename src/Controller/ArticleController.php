<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('article/home.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/{article}", name="article")
     * @param Article $article
     * @return Response
     */
    public function show(Article $article): Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($article);
        return $this->render('article/article.html.twig', [
            "article"=>$article
        ]);
    }

    /**
     * @Route("/article", name="create_article")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createArticle(Request $request): Response
    {
        if($this->getUser()){
            $article = new Article();
            $user = $this->getUser();
            $form = $this->createForm(ArticleFormType::class,$article);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){

                $image = $form->get('image')->getData();
                if($image){
                    $file = md5(uniqid()). '.' . $image->guessExtension();
                    $image->move(
                        $this->getParameter('images_directory'),
                        $file
                    );
                    $article
                        ->setPicture($file)
                    ;
                }
                $article
                    ->setAuthor($user)
                    ->setCreatedAt(new \DateTime('now'))
                    ->setUpdatedAt(new \DateTime('now'))
                ;

                $em = $this->getDoctrine()->getManager();
                $em->persist($article);
                $em->flush();
            }

            return $this->render('article/articleForm.html.twig',[
                'form' => $form->createView(),
            ]);
        }
        else{
            return $this->redirectToRoute('app_login');
        }
    }
}
