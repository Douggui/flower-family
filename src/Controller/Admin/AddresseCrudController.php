<?php

namespace App\Controller\Admin;

use App\Entity\Addresse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CountryField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AddresseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Addresse::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
        
            TextField::new('firstName')->setLabel('Nom'),
            TextField::new('LastName')->setLabel('Prénom'),
            TextField::new('name')->setLabel('Nom de l\'adresse'),
            TextField::new('addresse')->setLabel('Adresse'),
            TextField::new('postal')->setLabel('Code postale'),
            TextField::new('city')->setLabel('Ville'),
            CountryField::new('country')->setLabel('Pays'),
            TelephoneField::new('phone')->setLabel('Téléphone'),
            AssociationField::new('user')->setLabel('Utilisateur'),
        ];
    }
    
}