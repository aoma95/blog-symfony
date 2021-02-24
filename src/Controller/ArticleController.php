<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
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
}
