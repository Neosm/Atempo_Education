<?php

namespace App\Form;

use App\Entity\Articles;
use App\Entity\Categories;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article'
            ])
            ->add('illustrations', FileType::class, [
                'label' => 'Illustration',
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
                ],
                'attr' => [
                    'accept'=>'.jpg, .jpeg, .png'
                ]
            ])
            ->add('content', CKEditorType::class, [
                    'label' => 'Contenu de l\'article'
            ])
            ->add('categories', EntityType::class, [
                'class' => Categories::class,
                'label' => 'Catégories',
                'multiple'=>false,
                'attr' => [
                    'placeholder' => 'Catégories',
                    'class'=>'select-users-search'
                ]
            ])
            ->add('submit',SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class'=>'btn btn-info btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
