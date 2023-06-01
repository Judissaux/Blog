<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use App\Repository\MenuRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class MenuCrudController extends AbstractCrudController
{   

    const MENU_PAGES = 0;
    const MENU_ARTICLES = 1;
    const MENU_LINKS = 2;
    const MENU_CATEGORIES = 3;

    public function __construct(
        private RequestStack $requestStack,
        private MenuRepository $menuRepo){}

    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    // Fonction qui génére la requête qui recherche les différents menus
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $subMenuIndex = $this->getSubMenuIndex();
        // Requete permet d'avoir uniquement les menu non null sur chaque onglet
        return $this->menuRepo->getIndexQueryBuilder($this->getFieldNameFromSubMenuIndex($subMenuIndex));
    }

    public function configureCrud(Crud $crud): Crud
    {
        $subMenuIndex = $this->getSubMenuIndex();
        
        // PERMET DE MODIFIER LE NOM DU BOUTON
        $entityLabelInSingular = 'un menu';

        // PERMET DE MODIFIER LES CHAMPS DE CHAQUE HAUT DE PAGE en utilisant la fonction "match" qui permet de comparer une exepression avec une ou plusieur condition
        $entityLabelInPlural = match ($subMenuIndex) {
            //si submenuindex vaut 1 on écrit Article ect...
            self::MENU_ARTICLES => 'Articles',
            self::MENU_CATEGORIES => 'Catégories',
            self::MENU_LINKS=> 'Liens personnalisés',
            default => 'Pages'
        };

        return $crud
        // Modifie le label du bouton d'ajout du menu
            ->setEntityLabelInSingular($entityLabelInSingular)
        // Modifie le label des différente page de menu dans easyAdmin
            ->setEntityLabelInPlural($entityLabelInPlural);
    }
   
 
    
    
    public function configureFields(string $pageName): iterable
    {   
        // on récupére l'entier qui correspond au menu 
        $subMenuIndex = $this->getSubMenuIndex();
        
        
        // Decomposition "yield typedechamp::new('nomdelavariabledansl'entité , label")
       yield TextField::new('name', 'Titre de la navigation');

       yield NumberField::new('menuOrder', 'Ordre');
       
       // on modifie le champs en fonction du menu
       yield $this->getFieldFromSubMenuIndex($subMenuIndex)
        ->setRequired(true);

       yield BooleanField::new('isVisible', 'Visible');

       yield AssociationField::new('subMenus', 'Sous-élément');
    }


    private function getFieldNameFromSubMenuIndex(int $subMenuIndex)
    {   
        // Même technique qui permet de matcher une correspondance par rapport à une exigeance et de renvoyé une réponse en fonction du résultat
            return  match ($subMenuIndex) {
                // si subMenuIndex vaut la constant menu_article qui vaut 1 alors on aura 'article'
                self::MENU_ARTICLES => 'article',
                self::MENU_CATEGORIES => 'category',
                self::MENU_LINKS=> 'link',
                default => 'page'
            };
    }

    // FONCTION QUI PERMET D'AVOIR SOI UN TEXTFIELD SI IL Y A UN LIEN A METTRE SINON UN ASSOCIATION FIELD
    private function getFieldFromSubMenuIndex(int $subMenuIndex)
    {
        $fieldName = $this->getFieldNameFromSubMenuIndex($subMenuIndex);

        return ($fieldName == 'link') ? TextField::new($fieldName,'Lien') : AssociationField::new($fieldName);
    }
    

    private function getSubMenuIndex(): int
    {   
        // On récupérer un tableau avec en referer l'adresse
        $url = $this->requestStack->getMainRequest()->query->all();

        // Partie lorsque l'on clique sur le bouton créer menu

        foreach ($url as $key => $value) {
                // Lorsque l'on clique sur le bouton "créer un menu" on récupére le referer et donc l'adresse
            if( 'referrer' === $key){
                // On récupére l'ensemble du submenuindex=?
                $val = strstr($value, 'submenuIndex');
                // On prend le 13 éme caracté qui correspond au chiffre
                $val = substr($val,13);
                // on le retourne
                return $val;
            }
        }
          // Permet de récupérer le numéro aprés le submenuIndex
          return $this->requestStack->getMainRequest()->query->getInt('submenuIndex');
    }
}
