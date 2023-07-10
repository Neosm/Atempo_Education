<?php

namespace App\Form;

use App\Entity\Delay;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DelayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('delayMinutes', IntegerType::class, [
            'label' => 'Minutes de retard',
        ])
        ->add('student', EntityType::class, [
            'label' => 'Élèves',
            'class' => Users::class,
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
            'data_class' => Delay::class,
        ]);
    }
}
