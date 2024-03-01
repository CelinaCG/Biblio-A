<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdvancedSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         
            ->add('titre', TextType::class, [
                'required' => false,
            ])
            ->add('auteur', TextType::class, [
                'required' => false,
            ])
            ->add('coauteur', TextType::class, [
                'required' => false,
            ])
            ->add('editeur', TextType::class, [
                'required' => false,
            ])
            ->add('annee', IntegerType::class, [
                'required' => false,
            ])
            ->add('langue', TextType::class, [
                'required' => false,
            ])
            ->add('typeDocument', TextType::class, [
                'required' => false,
            ])
            ->add('categorie', TextType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
        ]);
    }
}
