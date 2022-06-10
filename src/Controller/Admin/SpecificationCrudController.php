<?php

namespace App\Controller\Admin;

use App\Entity\Specification;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
class SpecificationCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Specification::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
           
            TextField::new('name')->setLabel('spécificité du produit'),
            AssociationField::new('subCategory')->setLabel('sous-catégorie')
           
        ];
    }

}