<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Commentary;
use App\Form\ArticleFormType;
use App\Form\CommentaryFormType;
use App\Repository\ArticleRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     * @param ArticleRepository $articleRepository
     * @return Response
     */
    public function index(ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAll();
//        $articles = $this->getDoctrine()->getRepository(Article::class)->findAllArticle();

        return $this->render('article/home.html.twig', [
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/article/{article}", name="article")
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function show(Article $article, Request $request): Response
    {
        $user = $this->getUser();
        $article = $this->getDoctrine()->getRepository(Article::class)->find($article);
        $commentary = new Commentary();
        $form = $this->createForm(CommentaryFormType::class,$commentary);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            if($this->getUser()){
                $commentary
                    ->setAuthor($user)
                    ->setContent($form->get('content')->getData())
                    ->setArticle($article)
                    ->setCreateAt(new \DateTime('now'));
                $em = $this->getDoctrine()->getManager();
                $em->persist($commentary);
                $em->flush();
            }
        }
        return $this->render('article/article.html.twig', [
            "article"=>$article,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/article", name="create_article")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_ADMIN", statusCode=404, message="Pas d'accée"))
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

    /**
     * @Route("/article/{article}", name="article" ,methods={"DELETE"})
     * @IsGranted ("ROLE_ADMIN")
     * @param Article $article
     * @param Request $request
     * @return Response
     */
    public function delete(Article $article, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();
        return $this->render('article/article.html.twig');
    }
}
