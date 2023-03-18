<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\Type\CommentType;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    #[Route('/article/{slug}', name: 'article_show')]
    // Le ? avant le type permet de rendre cette valeur nullable lors de l'appel de la méthode
    public function show(?Article $article, CommentService $commentService): Response
    {

        if(!$article) {
            $this->redirectToRoute('app_home');
        }
        // Chaque commentaire créer sera associé à un article
        $comment = new Comment($article);

        $commentForm = $this->createForm(CommentType::class,$comment); 

        return $this->render('article/show.html.twig', [
            'article' => $article,
            'commentForm' => $commentForm->createView(),
            'comments' => $commentService->getPaginatedComments($article)

        ]);
        
    }
}
