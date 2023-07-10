<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EditProfileType extends AbstractType
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
                    'Placeholder'=>'exemple@exemple.com'
                ]
            ])->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr'=>[
                    'Placeholder'=>'0607080901'
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
            ->add('submit',SubmitType::class, [
                'label' => 'Changer ses informations',
                'attr' => [
                    'class'=>'btn btn-info btn-block'
                ]
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
