<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('user')->setLabel('Urilisateurs'),
            DateTimeField::new('createdAt')->setLabel('Passé le'),
            TextField::new('reference')->setLabel('Référence'),
            TextField::new('deliveryMethod')->setLabel('Mode de livraison'),
            TextField::new('addresse')->setLabel('Adresse'),
            MoneyField::new('total')->setCurrency('EUR')->setLabel('Total'),
            BooleanField::new('isDelivered')->setLabel('Livré/pas'),
        ];
    }
    
}