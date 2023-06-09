<?php

namespace App\Form\Type;

use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class,[
                'label' => 'Votre message'
            ])
            
            ->add('article', HiddenType::class)

            ->add('send',SubmitType::class, [
                'label' => 'Envoyer'
            ]);
        
        // Permet de modifier le hiddenType 'article' en envoyant l'id au lieu du title et la seconde fonction récupérer le titre    
        $builder->get('article')
            ->addModelTransformer(new CallbackTransformer(
                fn (Article $article) =>$article->getId(),
                fn (Article $article) =>$article->getTitle(),               
            ));
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {   
        // Permet de dire que ce formulaire sera toujourds associés avec un objet de type Comment
        $resolver->setDefaults([
            'data_class' => Comment::class,
            'csrf_token_id' => 'comment-add'
        ]);
    }
}