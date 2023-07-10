<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbsenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('student', EntityType::class, [
                'label' => 'Élèves',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :val')
                    ->setParameter('val', '%["ROLE_STUDENT"]%');
                },
                'choice_label' => 'username',
                'attr' => [
                    'class' => 'students-field select2'
                ],
                'label_attr' => [
                    'class' => 'label-students',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,
        ]);
    }
}
