<?php

namespace App\Form;

use App\Entity\Addresse;
use App\Entity\DeliveryMethod;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $user=$options['user'];
        //dd($user);
        $builder
        ->add('Addresse',EntityType::class,[
            'label' => 'Choisissez votre adresse de livraison',
            //'choice_label' => 'addresse',
             //'choices'=>$user->getAddresses(),
            'required' => true,
            'class' => Addresse::class, // avec quelle classe faire le lien pour le formulaire ( chercher les propriétés à afficher dans le formulaire)
            'multiple' => false,
            'expanded' => true, // on veut des radio bouton
           
        ])
        ->add('DeliveryMethod',EntityType::class,[
            'label' => 'Choisissez votre mode  de livraison',
                'choice_label' => 'name',
                'required' => true,
                'class' => DeliveryMethod::class, // avec quelle classe faire le lien pour le formulaire ( chercher les propriétés à afficher dans le formulaire)
                'multiple' => false,
                'expanded' => true,
        ])
           
            ->add('submit',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'user'=>[]
        ]);
    }
}