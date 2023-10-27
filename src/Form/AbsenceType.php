<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\Event;
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
        $ecole = $options['ecole'];
        $builder
            ->add('student', EntityType::class, [
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
                'attr' => [
                    'class' => 'students-field select2'
                ],
                'label_attr' => [
                    'class' => 'label-students',
                ],
                
            ]);
            

            if ($options['creer_absence_professeur']) {
                $userid = $options['userid'];
    
                $builder
                    ->add('event', EntityType::class,[
                        'label' => 'Cours conserné',
                        'class' => Event::class,
                        'choice_label' => 'getTitleAndStart',
                        'query_builder' => function (EntityRepository $er) use ($userid) {
                            return $er->createQueryBuilder('e')
                                ->andWhere('e.teacher = :userid')
                                ->setParameter('userid', $userid);
                        },
                    ]);
            } elseif ($options['creer_absence_admin']) {
                $builder
                    ->add('event', EntityType::class,[
                        'label' => 'Cours conserné',
                        'class' => Event::class,
                        'choice_label' => 'getTitleAndStart',
                        'query_builder' => function (EntityRepository $er) use ($ecole) {
                            return $er->createQueryBuilder('ev')
                                ->join('ev.ecoles', 'ec')
                                ->andWhere('ec.id = :ecole')
                                ->setParameter('ecole', $ecole);
                        },
                    ]);
            }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Absence::class,            
            'creer_absence_professeur' => false,        
            'creer_absence_admin' => false, // Valeur par défaut pour l'option personnalisée
            'userid' => null,
            'ecole' => null,
        ]);
    }
}
