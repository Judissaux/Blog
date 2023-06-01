<?php

namespace App\EventSubscriber;

use App\Model\TimestampedInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


// Permet de rendre automatique l'ajout de date lors de la création des entités qui implémente l'interface TimeStamped
class AdminSubscriber implements EventSubscriberInterface
{
   public static function getSubscribedEvents()
   {
     return[
        //ajout des éléments qui seront exécuté avant la création d"une entité
        BeforeEntityPersistedEvent::class => ['setEntityCreatedAt'],
        BeforeEntityUpdatedEvent::class => ['setEntityUpdatedAt']
     ];
    }

    public function setEntityCreatedAt(BeforeEntityPersistedEvent $event)
    {   
        // permet de récupérer l'entité
        $entity = $event->getEntityInstance();
        //Grace à la création de l'interface Timestamped on peux créer une condition!
        if(!$entity instanceof TimestampedInterface){
            return;
        }
        // Sinon on set à la date du jour
        $entity->setCreatedAt(new \DateTime());
    }

    public function setEntityupdatedAt(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!$entity instanceof TimestampedInterface){
            return;
        }

        $entity->setUpdatedAt(new \DateTime());
    }
}
