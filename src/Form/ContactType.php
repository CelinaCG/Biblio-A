<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 3,  'minMessage' => "Veuillez metre un nom valide.",
                        'max' => 50,  'maxMessage' => "Votre nom ne peut pas contenir plus de 50 caractères.",
                    ]),
                    new Regex([
                        'pattern' => "/\d/",
                        'match' => false,
                        'message' => "Votre nom ne peut pas contenir de numéro"
                    ]),
                    new NotBlank(['message' => 'Merci d\'entrer votre nom.'])
                   
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Length([
                        'min' => 3,  'minMessage' => "Veuillez metre un prénom valide.",
                        'max' => 50,  'maxMessage' => "Votre prénom ne peut pas contenir plus de 50 caractères.",
                    ]),
                    new Regex([
                        'pattern' => "/\d/",
                        'match' => false,
                        'message' => "Votre prénom ne peut pas contenir de numéro"
                    ]),
                    new NotBlank(['message' => 'Merci d\'entrer votre prénom'])
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Regex([
                        'pattern' => '/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6})*$/',
                        // le regex cherche les caractèristiques suivantes: 
                        // ^ asserts position at start of the string. 
                        // a-z matches a single character in the range between a (index 97) and z (index 122) (case sensitive)
                        // A-Z matches a single character in the range between A (index 65) and Z (index 90) (case sensitive)
                        // 0-9 matches a single character in the range between 0 (index 48) and 9 (index 57) (case sensitive)
                        // ._%- matches a single character in the list ._%- (case sensitive)
                        // + matches the previous token between one and unlimited times, as many times as possible, giving back as needed
                        // @ matches the character @ with index 6410 (4016 or 1008) literally (case sensitive)
                        // .- matches a single character in the list .- (case sensitive)
                        // \. matches the character . with index 4610 (2E16 or 568) literally (case sensitive)
                        // {2,6} matches the previous token between 2 and 6 times, as many times as possible, giving back as needed 
                        // * matches the previous token between zero and unlimited times, as many times as possible, giving back as needed 
                        // $ asserts position at the end of the string, or before the line terminator right at the end of the string (if any)
                        'message' => 'Email non valide'
                    ]),
                    new NotBlank(['message' => 'Merci d\'entrer un email.'])
                ]
            ])
            ->add('sujet', ChoiceType::class,
            array(
                'choices' => array(
                    'Sélectionner' => 'Sélectionner',
                    'Informations pratiques' => 'Informations pratiques',
                    'Question documentaire' =>'Question documentaire',
                    'Dysfonctionnement' => 'Dysfonctionnement',
                    'Autre' => 'Autre',
                ),
            ))
            ->add('message', TextareaType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Merci de remplir le champ.']),
                    new Length(['min' => 10,  'minMessage' => "Le nombre de caractères est insuffisant.",])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
