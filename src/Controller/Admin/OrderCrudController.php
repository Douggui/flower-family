<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use DateTime;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
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
            IntegerField::new('id')->setLabel('commande n°'),
            AssociationField::new('user')->setLabel('Urilisateurs'),
            DateTimeField::new('createdAt')->setLabel('Passé le'),
            TextField::new('reference')->setLabel('Référence'),
            TextField::new('deliveryMethod')->setLabel('Mode de livraison'),
            TextField::new('addresse')->setLabel('Adresse'),
            MoneyField::new('total')->setCurrency('EUR')->setLabel('Total'),
            BooleanField::new('isPaid')->setLabel('payé'),
            BooleanField::new('isDelivered')->setLabel('Livré/pas'),
            ChoiceField::new('status')->setChoices([
                'en attente de paiment'=>Order::ISPENDING,
                'payé'                 =>Order::PAID,
                'annulé'               => Order::CANCELED,
                'livré'                =>Order::SENT
            ])
            
        ];
    }
    
}