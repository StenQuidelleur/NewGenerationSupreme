<?php

namespace App\Form;

use App\Entity\Address;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom *'])
            ->add('lastname', TextType::class, ['label' => 'Nom *'])
            ->add('street', TextType::class, ['label' => 'Numéro et nom de la rue *'])
            ->add('complement', TextType::class, ['label' => 'Complément','required' => false])
            ->add('city', TextType::class, ['label' => 'Ville *'])
            ->add('country', TextType::class, ['label' => 'Pays *'])
            ->add('zip_code', TextType::class, ['label' => 'Code Postal *'])
            ->add('phone', TextType::class, ['label' => 'Téléphone *'])
            ->add('company_name', TextType::class, ['label' => 'Nom de l\'entreprise (facultatif)', 'required' => false ])
            ->add('shipping_comment', TextareaType::class, ['label' => 'Commentaire de livraison', 'required' => false])
            //->add('shipping_address', CheckboxType::class, ['label' => 'Addresse de livraison', 'required' => false])
            //->add('billing_address', CheckboxType::class, ['label' => 'Addresse de facturation', 'required' => false])
            //->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
        ]);
    }
}
