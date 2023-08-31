<?php

namespace App\Form;

use App\Entity\StudentClass;
use App\Entity\Users;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TypeTextType::class, [
                "label" => 'Nom de la classe'
            ])
            ->add('students', EntityType::class, [
                'label' => 'Élèves',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $qb = $er->createQueryBuilder('u')
                        ->andWhere('u.roles LIKE :val')
                        ->andWhere('u.studentClass IS NULL')
                        ->setParameter('val', '%["ROLE_STUDENT"]%');

                    // Si vous êtes en mode d'édition et que des élèves sont déjà associés à la classe,
                    // excluez-les de la requête pour éviter de les réafficher
                    if ($options['studentsInClass']) {
                        $qb->orWhere('u IN (:studentsInClass)')
                            ->setParameter('studentsInClass', $options['studentsInClass']);
                    }

                    return $qb;
                },
                'choice_label' => 'UserIdentifier',
                'multiple' => true,
                'required' =>false,
                'attr' => [
                    'class' => 'students-field'
                ],
                'label_attr' => [
                    'class' => 'label-students',
                ],
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
            'data_class' => StudentClass::class,
            'studentsInClass' => [], // Ajoutez cette option pour recevoir les étudiants pré-remplis
        ]);
    }
}
