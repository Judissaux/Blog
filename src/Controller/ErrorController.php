<?php

namespace App\Controller;

use Twig\Environment;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController
{
    
    public function show(FlattenException $exception, Environment $env): Response
    {
        $view = "bundles/TwigBundle/Exception/error{$exception->getStatusCode()}.html.twig";

        if(!$env->getLoader()->exists($view)){
            $view = "bundles/TwigBundle/Exception/error.html.twig";
        }

        return $this->render($view);
    }
}