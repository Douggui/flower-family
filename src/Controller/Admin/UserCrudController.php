<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setFormOptions([
            'validation_groups' => ['registration']
        ]);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
           
            TextField::new('firstName')->setLabel('Prénom'),
            TextField::new('lastName')->setLabel('Nom'),
            EmailField::new('email')->setLabel('Email'),
            BooleanField::new('isActive')->setLabel('Active/non'),
            ChoiceField::new('roles')->setChoices(['Admin' => 'ROLE_ADMIN', 'utilisateur' => 'ROLE_USER'])->allowMultipleChoices()->setLabel('Role'),
            TextField::new('password')->setFormType(PasswordType::class)->setLabel('Mot de passe')->onlyWhenCreating(),
            TextField::new('confirmPassword')->setFormType(PasswordType::class)->setLabel('Confirmation mot de passe')->onlyWhenCreating(),


           
        ];
    }
    
}
