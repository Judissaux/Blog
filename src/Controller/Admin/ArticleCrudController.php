<?php

namespace App\Controller\Admin;

use App\Entity\Article;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ArticleCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Article::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
       yield TextField::new('title');
       //Set targetFieldName Permet de récupérer le nom auquelle le slug est associés 
       yield SlugField::new('slug')->setTargetFieldName('title');

       yield TextEditorField::new('content');

       yield TextField::new('featuredText');
    
       yield AssociationField::new('categories');

       yield AssociationField::new('featuredImage');
       //Hide on form permet de cacher le champs lors de la phase de création ou edition
       yield  DateTimeField::new('createdAt')->hideOnForm();

       yield DateTimeField::new('updatedAt')->hideOnForm();
    }
    
}
