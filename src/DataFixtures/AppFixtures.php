<?php

namespace App\DataFixtures;

use App\Entity\Option;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $options[] = new Option('Texte du copyright', 'blog_copyright', 'Tous droits réservés', TextType::class);
        $options[] = new Option('Nombre d\'articles par page', 'blog_articles_limit', 5 , NumberType::class);
        $options[] = new Option('Tout le monde peu s\'inscrire', 'user_can_register', 'true', CheckboxType::class);

        foreach($options as $option){
            $manager->persist($option);
        }
        
        $manager->flush();
    }
}
