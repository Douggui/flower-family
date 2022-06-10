<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdatePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email',EmailType::class,['disabled'=>true])
            ->add('firstName',TextType::class,['disabled'=>true])
            ->add('lastName',TextType::class,['disabled'=>true])
            //->add('roles')
            ->add('oldPassword',PasswordType::class,['required'=>true])
            ->add('newPassword',PasswordType::class,['required'=>true])
            ->add('confirmNewPassword',PasswordType::class,['required'=>true])
            ->add('submit',SubmitType::class)
            
           // ->add('isActive')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
