<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Option;
use App\Model\WelcomeModel;
use App\Form\Type\WelcomeType;
use App\Service\ArticleService;
use App\Repository\CategoryRepository;
use App\Service\OptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ArticleService $articleService, CategoryRepository $category): Response
    {
        return $this->render('home/index.html.twig', [
            'articles' => $articleService->getPaginatedArticles(),
            'categories' => $category->findAll()
        ]);
        
    }
    #[Route('/welcome', name: 'app_welcome')]
    public function welcome(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        OptionService $optionService) :response
    {   
        if($optionService ->getValue(WelcomeModel::SITE_INSTALLED_NAME)){
            return $this->redirectToRoute('app_home');
        }
        $welcomeForm = $this->createForm(WelcomeType::class,new \App\Model\WelcomeModel());
        
        $welcomeForm->handleRequest($request);

        if($welcomeForm->isSubmitted() && $welcomeForm->isValid()){

            $data = $welcomeForm->getData();

            $siteTitle = new Option(WelcomeModel::SITE_TITLE_LABEL, WelcomeModel::SITE_TITLE_NAME,$data->getSiteTitle(),TextType::class);
            $siteInstalled = new Option(WelcomeModel::SITE_INSTALLED_LABEL, WelcomeModel::SITE_INSTALLED_NAME,true,null);

            $user= new User($data->getUsername());
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($passwordHasher->hashPassword($user,$data->getPassword()));

            $em->persist($siteTitle);
            $em->persist($siteInstalled);            
            $em->persist($user);

            $em->flush();

            return  $this->redirectToRoute('app_home');

        }
        
        return $this->render('home/welcome.html.twig',[
            'welcomeForm' =>$welcomeForm->createView()
        ]);
    }
}
