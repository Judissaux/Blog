<?php

namespace App\EventListener;

use App\Service\DataBaseService;
use Symfony\Component\Routing\RouterInterface;
use Doctrine\DBAL\Exception\ConnectionException;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionListener
{
    public function __construct(
        private DataBaseService $databaseService,
        private RouterInterface $router       
    )
    {        
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if($exception instanceof ConnectionException || $exception instanceof TableNotFoundException) {
            $this->databaseService->createDatabase();

            $event->setResponse(new RedirectResponse(($this->router->generate('app_welcome'))));
        }
    }
}