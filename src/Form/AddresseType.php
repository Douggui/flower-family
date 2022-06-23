<?php

namespace App\Form;

use App\Entity\Addresse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddresseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName',TextType::class,['required'=>true])
            ->add('lastName',TextType::class,['required'=>true])
            ->add('phone',TelType::class,['required'=>true])
            ->add('name',TextType::class,['required'=>true])
            ->add('addresse',TextType::class,['required'=>true])
            ->add('postal',TextType::class,['required'=>true])
            ->add('city',TextType::class,['required'=>true])
           // ->add('country',CountryType::class,['required'=>true])
            ->add('submit',SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Addresse::class,
        ]);
    }
}