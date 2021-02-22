<?php

namespace App\Controller;

use App\Entity\Article;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {
//        $article = new Article();
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
        return $this->render('article/home.html.twig', [
            'articles' => $articles,
        ]);
    }
}
