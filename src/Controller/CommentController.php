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
                // Sinon on envoie un message d'erreur en format JSON
                'code' => 'INVALID_CSRF_TOKEN'],
                Response::HTTP_BAD_REQUEST);
        }

        $article = $articleRepo->FindOneBy(['id' => $commentData['article']]);
        if(!$article) {
            return $this->json([
               'code' => 'ARTICLE_NOT_FOUND'     
            ], Response::HTTP_BAD_REQUEST);
        }

        // On crée un nouveau commentaire avec un article associé
        $comment = new Comment($article);
        // Ici on récupére l'utilisateur
        $user = $this->getUser();

        // Petite condition qui permet de savoir si l'utilisateur est connecté
        if(!$user){
            return $this->json([
                'code' => 'USER_NOT_AUTHENTICATED_FULLY'
            ], Response::HTTP_BAD_REQUEST);
        }

        // On insére le contenu du commentaire
        $comment->setContent($commentData['content']);
        // on associe l'utilisateur
        $comment->setUser($user);
        // On met la date du commentaire
        $comment->setCreatedAt(new \DateTime());

        $em->persist($comment);
        $em->flush();


        $html = $this->renderView('comment/index.html.twig', [
            'comment' => $comment
        ]);

        return $this->json([
            'code' => 'COMMENT_ADDED_SUCCESFULLY',
            'message' => $html,
            // Compte le nbr de commentaire associé à l'article car on passe l'article en critére pour le repository
            'numberOfComments' =>$commentRepo->count(['article' => $article ])
        ]);
    }
}
