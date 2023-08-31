<?php

namespace App\Form;

use App\Entity\Delay;
use App\Entity\Event;
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
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->andWhere('u.roles LIKE :val')
                ->setParameter('val', '%["ROLE_STUDENT"]%');
            },
            'choice_label' => 'UserIdentifier',
            'attr' => [
                'class' => 'students-field select2'
            ],
            'label_attr' => [
                'class' => 'label-students',
            ],
        ]);

        if ($options['ajouter_creer_delay']) {
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
        }elseif ($options['admin_creer_delay']) {

            $builder
                ->add('event', EntityType::class,[
                    'label' => 'Cours conserné',
                    'class' => Event::class,
                    'choice_label' => 'getTitleAndStart',
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Delay::class,
            'ajouter_creer_delay' => false,
            'admin_creer_delay' => false, // Valeur par défaut pour l'option personnalisée
            'userid' => null,
        ]);
    }
}
