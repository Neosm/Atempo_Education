<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Materials;
use App\Entity\Matieres;
use App\Entity\Room;
use App\Entity\StudentClass;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class EventFormType extends AbstractType
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('matieres', EntityType::class, [
                'label' => 'Matière',
                'class' => Matieres::class,
                'choice_label' => 'name',
            ])
            ->add('start', DateTimeType::class, [
                'label' => 'Heure de début',
                'widget' => 'single_text',
                'html5' => false,
                'attr' => [
                    'class' => 'form-control datetimepickr my-flatpickr',
                    'data-route' => '/agenda/api/reserved_rooms', // Route pour récupérer les salles réservées
                ],
            ])
            ->add('duration', IntegerType::class, [
                'label' => 'Durée (en minutes)',
            ])
            ->add('teacher', EntityType::class, [
                'label' => 'Professeur',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :val')
                    ->setParameter('val', '%["ROLE_TEACHER"]%');
                },
                'choice_label' => 'username',
            ])
            ->add('materials', EntityType::class, [
                'label' => 'Équipement de la salle (non obligatoire)',
                'class' => Materials::class,
                'choice_label' => 'name',
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'materials-field select2 materials-checkboxes'
                ],
                'label_attr' => [
                    'class' => 'label-materials',
                ],
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'placeholder' => 'Choisir une salle pour le cours',
                'choice_label' => 'name',
                'label' => 'Salle',
                'attr' => [
                    'class' => 'room-select',
                ],
            ])
            ->add('switch', CheckboxType::class, [
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'custom-switch-input',
                    'role' => 'switch',
                ],
                'label_attr' => [
                    'class' => 'custom-switch-label',
                ],
            ])
            ->add('studentClass', EntityType::class, [
                'label' => 'Classe',
                'class' => StudentClass::class,
                'choice_label' => 'name',
                'required' => false,
                'attr' => [
                    'class' => 'student-class-field'
                ],
                'label_attr' => [
                    'class' => 'label-studentClass',
                ],
            ])
            ->add('students', EntityType::class, [
                'label' => 'Élèves',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :val')
                    ->setParameter('val', '%["ROLE_STUDENT"]%');
                },
                'choice_label' => 'username',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'students-field select2'
                ],
                'label_attr' => [
                    'class' => 'label-students',
                ],
            ])
            ->add('reservedRooms', HiddenType::class, [
                'mapped' => false,
            ]);



        // Ajouter l'écouteur d'événement pour le champ de commentaire
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // Récupérer la date de fin de l'événement
            $startDate = $data->getStart();
            // Vérifier si la date de fin est passée
            if ($startDate !== null && $startDate < new \DateTime()) {
                // Activer le champ de commentaire
                $form->add('comment', TextareaType::class, [
                    'label' => 'Commentaire',
                    'required' => false,
                ]);
            }
            
        });

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof Event && $data->getId() !== null) {
                // Pré-remplir les salles réservées si l'événement existe déjà
                $reservedRooms = $data->getRoom();
                if ($reservedRooms !== null) {
                    $form->get('reservedRooms')->setData($reservedRooms->getId());
                }
            }
        });

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}