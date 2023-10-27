<?php

namespace App\Form;

use App\Entity\Categories;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoriesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $ecole = $options['ecole'];
        $builder
            ->add('name', TextType::class, [
                'label' => 'Titre de la catégorie'
            ])
            ->add('parent', EntityType::class, [
                'label' => 'Catégorie rattachée',
                'class'=>Categories::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($ecole) {
                    return $er->createQueryBuilder('c')
                        ->join('c.ecoles', 'ec')
                        ->andWhere('ec.id = :ecole')
                        ->setParameter('ecole', $ecole);
                },
                'attr'=>[
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
            'data_class' => Categories::class,
            'ecole' => null,
        ]);
    }
}
