<?php

namespace App\Form;

use App\Entity\Book;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isbn', TextType::class, [
                'label' => 'Code ISBN'
            ])
            ->add('ean', TextType::class, [
                'label' => 'Code EAN'
            ])
            ->add('langue', TextType::class, [
                'label' => 'Langue'
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('editeur', TextType::class, [
                'label' => 'Editeur'
            ])
            ->add('annee', IntegerType::class, [
                'label' => 'Année'
            ])
            ->add('format', TextType::class, [
                'label' => 'Format'
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur'
            ])
            ->add('coauteur', TextType::class, [
                'label' => 'Co-auteur'
            ])
            ->add('cote', TextType::class, [
                'label' => 'Cote'
            ])
            ->add('typeDocument', TextType::class, [
                'label' => 'Type de document'
            ])
            ->add('categorie', TextType::class, [
                'label' => 'Catégorie'
            ])
            ->add('resume', TextareaType::class, [
                'label' => 'Résumé'
            ])
            ->add('imageLivre', FileType::class, [
                'label' => 'Couverture',
                'mapped' => false,
                // Pour ne pas re-uploader chaque fois qu'on édite la fiche
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
