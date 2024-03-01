<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Regex;
use Captcha\Bundle\CaptchaBundle\Form\Type\CaptchaType;
use Captcha\Bundle\CaptchaBundle\Validator\Constraints\ValidCaptcha;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'mapped' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'mapped' => true
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email([
                        'message' => 'Merci d\'entrer un email valide.',
                    ]),
                ],
            ])
      
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les CGU',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez acceptez nos conditions générales d\'utilisation pour pouvoir utiliser nos services.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [ 
                'type' => PasswordType::class,
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci d\'entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' =>"/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/",
                        // Six caractères au moins, au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial
                        'message' => 'Mot de passe invalide. Votre mot de passe doit avoir au mois six caractères, au moins une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial.'
                    ])
                ],
                'first_options' => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation du mot de passe'],
                'invalid_message' => 'Vos mots de passe doivent être identiques.'
            ])
            ->add('captchaCode', CaptchaType::class, array(
                'captchaConfig' => 'CaptchaRegistration',
                'label' => 'Merci d\'écrire les lettres sur l\'image',
                'constraints' => [
                    new ValidCaptcha([
                        'message' => 'Captcha invalide, réessayez s\'il vous plait',
                    ])
                ]
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
