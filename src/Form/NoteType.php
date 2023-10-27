<?php

namespace App\Form;

use App\Entity\Matieres;
use App\Entity\Notes;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ecole = $options['ecole'];
        $builder
            ->add('note', IntegerType::class, [
                'label' => 'Note',
                'attr' => [
                    'min' => 0,
                    'max' => 20,
                ]
            ])
            ->add('date', DateType::class, [
                'label' => 'Date de la note',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control datetimepickr my-flatpickr',
                ],
            ])
            ->add('coefficient', IntegerType::class, [
                'label' => 'Coefficient',
                'required' => false,
            ])
            ->add('user', EntityType::class, [
                'label' => 'Élèves',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) use ($ecole) {
                    return $er->createQueryBuilder('u')
                    ->join('u.ecoles', 'e')
                    ->andWhere('u.roles LIKE :val')
                    ->andWhere('e.id = :ecoleId')
                    ->setParameter('val', '%["ROLE_STUDENT"]%')
                    ->setParameter('ecoleId', $ecole->getId());
            },
                'choice_label' => 'UserIdentifier',
            ])
            ->add('matiere', EntityType::class, [
                'label' => 'Discipline',
                'class' => Matieres::class,
                'query_builder' => function (EntityRepository $er) use ($ecole) {
                    return $er->createQueryBuilder('m')
                        ->join('m.ecoles', 'ec')
                        ->andWhere('ec.id = :ecole')
                        ->setParameter('ecole', $ecole);
                },
                'choice_label' => 'name',
            ])
            ->add('submit',SubmitType::class, [
                'label' => 'Valider',
                'attr' => [
                    'class'=>'btn btn-info btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notes::class,
            'ecole' => null,
        ]);
    }
}
