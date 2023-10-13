<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'label'=>'Prénom',
                'attr'=>[
                    'placeholder'=>'Votre prénom'
                ]
            ])
            ->add('lastname', TextType::class, [
                'label'=>'Nom',
                'attr'=>[
                    'placeholder'=>'Votre nom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr'=>[
                    'Placeholder'=>'exemple@exemp.com'
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr'=>[
                    'Placeholder'=>'0607080901'
                ]
            ])->add('dateOfBirth', DateType::class, [
                'label' => "Date de naissance",
                'years' => range(date('Y') - 100, date('Y')),
            ])
            ->add('roles', ChoiceType::class, [
                'label' => 'Role de l\'utilisateur',
                'placeholder'=> 'Élève, Professeur, Secrétariat',
                'required' => true,
                'multiple' => false,
                'expanded' => false,
                'choices'=> [
                    'Élèves'=> "ROLE_STUDENT",
                    'Professeur'=> "ROLE_TEACHER",
                    'Secrétariat'=> "ROLE_ADMIN",
                ]
            ])
            ->add('thumbnail', FileType::class, [
                'label' => 'Photo de profile',
                'mapped'=>false,
                'multiple'=>false,
                'required'=>false,
                'row_attr'=>[
                    'class'=>'mb-3'
                ],
                'constraints' => [
                    new File([
                        'maxSize' => '600k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg',
                        ],
                        'mimeTypesMessage' => 'Choisissez une image valide',
                    ])
                ]
            ])
            ->add('password',RepeatedType::class, [
                'type'=> PasswordType::class,
                'invalid_message'=>"Les deux mots de passe ne sont pas semblables, veuillez les retaper",
                'label'=>'Mot de passe',
                'required'=>true,
                'first_options'=>[
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Entrer le mot de passe',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit faire minimum {{ limit }} caracters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label'=>'Mot de passe',
                    'attr'=>[
                        'PlaceHolder'=>'Votre mot de passe'
                    ]
                ],
                'second_options'=>[
                    'label'=>'Confirmez votre mot de passe',
                    'attr'=>[
                        'PlaceHolder'=>'Confirmez votre mot de passe'
                    ]
                ]
            ])
            ->add('submit',SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class'=>'btn btn-info btn-block'
                ]
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'Accepter les conditions d\'utilisation',
                'constraints' => [
                    new IsTrue([
                        'message' => 'Les conditions d\'utilisation doivent être acceptées',
                    ]),
                ],
            ])
        ;
        // Data transformer
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // transform the array to a string
                    return count($rolesArray) > 0? $rolesArray[0]: null;
                },
                function ($rolesString) {
                    // transform the string back to an array
                    return [$rolesString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
