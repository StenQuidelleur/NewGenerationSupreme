<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname',TextType::class,  ['label' => 'Prénom '])
            ->add('lastname',TextType::class,  ['label' => 'Nom'])
            ->add('email',TextType::class,  ['label' => 'Email'])
            ->add('date_birth', BirthdayType::class, ['label' => 'Date de Naissance', 'required' => false ])
            ->add('phone',TextType::class,  ['label' => 'Téléphone', 'required' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
