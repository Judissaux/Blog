<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class CommentController extends AbstractController
{
    #[Route('/ajax/comments', name: 'comment_add')]
    public function add(Request $request,CommentRepository $commentRepo , ArticleRepository $articleRepo, EntityManagerInterface $em, UserRepository $userRepo): Response
    {       
        // Ici on récupére les données émise par le formulaire graçe au nom donnée au formulaire dans le render 'comment'   
        $commentData = $request->request->all('comment');

        
        
        //Ici on vérifie que le token envoyé avec le commentaire correspond au token d'ajout de commentaire
        if(!$this->isCsrfTokenValid('comment-add',$commentData['_token'])){
            return $this->json([
                'code' => 'INVALID_CSRF_TOKEN'],
            Response::HTTP_BAD_REQUEST);
        }

        $article = $articleRepo->FindOneBy(['id' => $commentData['article']]);
        if(!$article) {
            return $this->json([
               'code' => 'ARTICLE_NOT_FOUND'     
            ], Response::HTTP_BAD_REQUEST);
        }

        $comment = new Comment($article);
        $user = $this->getUser();

        if(!$user){
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }
        $comment->setContent($commentData['content']);
        $comment->setUser($user);
        $comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();

        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);

        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESFULLY',
            'message' => $html,
            'numberOfComments' =>$commentRepo->count(['article' => $article ])
        ]);
    }
}
