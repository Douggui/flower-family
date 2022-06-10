<?php

namespace App\Controller\Admin;

use App\Entity\CaracteristicDetail;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
class CaracteristicDetailCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CaracteristicDetail::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
         
            TextField::new('detail')->setLabel('déscription'),
            AssociationField::new('product')->setLabel('nom du produit'),
            AssociationField::new('caracteristic')->setLabel('caractéristique'),
            
        ];
    }
    
}