<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DefinedNewPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class, [
                'disabled' => true,
                'label'=>'Mon Prénom'
            ])
            ->add('lastname', TextType::class, [
                'disabled' => true,
                'label'=>'Mon Nom'
            ])
            ->add('email', EmailType::class, [
                'disabled' =>true,
                'label'=>'Mon adresse Email'
            ])
            ->add('old_password', PasswordType::class,[
                'mapped' => false,
                'label'=> 'Mon mot de passe',
                'attr' => [
                    'PlaceHolder' => 'Veuillez saisir votre mot de passe'
                ]
            ])
            ->add('new_password',RepeatedType::class, [
                'type'=> PasswordType::class,
                'mapped'=>false,
                'invalid_message'=>"Les deux mots de passe ne sont pas semblables, veuillez les retaper",
                'label'=>'Mot de passe',
                'required'=>true,
                'first_options'=>[
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
                'label' => 'Mettre à jour'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
