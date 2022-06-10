<?php

namespace App\Controller\Admin;

use App\Entity\DeliveryMethod;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DeliveryMethodCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryMethod::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            
            TextField::new('name')->setLabel('Nom'),
            
        ];
    }

}