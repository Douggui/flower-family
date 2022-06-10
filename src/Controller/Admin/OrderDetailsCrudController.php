<?php

namespace App\Controller\Admin;

use App\Entity\OrderDetails;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class OrderDetailsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return OrderDetails::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('myOrder')->setLabel('Commande'),
            AssociationField::new('product')->setLabel('produit'),
            TextField::new('specification')->setLabel('spécification'),
            IntegerField::new('quantity')->setLabel('quantité'),
            MoneyField::new('price')->setCurrency('EUR')->setLabel('P.U'),
            MoneyField::new('total')->setCurrency('EUR')->setLabel('P.T'),
        ];
    }
    
}