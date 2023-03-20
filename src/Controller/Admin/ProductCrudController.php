<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\TextEditorType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            TextField::new('name')->setLabel('nom'),
            SlugField::new('slug')->setTargetFieldName('name'),
            TextEditorField::new('description')->setLabel('déscription'),
            TextareaField::new('metaDescription')->setLabel('meta description'),
            TextField::new('metaKeywords')->setLabel('meta keywords'),
            MoneyField::new('price')->setCurrency('EUR')->setLabel('prix'),
            AssociationField::new('subCategory')->setLabel('Nom de la sous-catégorie'),
            BooleanField::new('isBest')->setLabel('best Seller'),
            BooleanField::new('isNew')->setLabel('Nouveau'),
             AssociationField::new('options')->setCrudController(OptionCrudController::class)->setLabel('options'),
            
        ];
    }
    
}