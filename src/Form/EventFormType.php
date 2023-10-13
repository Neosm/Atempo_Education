<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Lecons;
use App\Entity\Materials;
use App\Entity\Matieres;
use App\Entity\Programmes;
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
use Symfony\Component\Form\Extension\Core\Type\UrlType;
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
            ->add('recurrence', ChoiceType::class, [
                'label' => 'Événement récurrent',
                'choices' => [
                    'Non' => false,
                    'Oui' => true,
                ],
                'multiple' => false,
            ])
            ->add('recurrenceFrequency', ChoiceType::class, [
                'label' => 'Fréquence de récurrence',
                'choices' => [
                    'Tous les jours' => 'daily',
                    'Toutes les semaines' => 'weekly',
                    'Tous les mois' => 'monthly',
                ],
                'expanded' => true, // Pour afficher en tant que radios
                'multiple' => false,
                'required' => false, // Rendre ce champ facultatif
                'attr' => [
                    'class' => 'form-control', // Ajoutez des classes CSS si nécessaire
                ],
            ])
            ->add('recurrenceEnd', DateTimeType::class, [
                'label' => 'Date de fin de récurrence',
                'widget' => 'single_text',
                'html5' => false,
                'required' => false, // Rendre ce champ facultatif
                'attr' => [
                    'class' => 'form-control datetimepickr', // Ajoutez des classes CSS si nécessaire
                ],
            ])
            ->add('teacher', EntityType::class, [
                'label' => 'Professeur',
                'class' => Users::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                    ->andWhere('u.roles LIKE :val')
                    ->setParameter('val', '%["ROLE_TEACHER"]%');
                },
                'choice_label' => 'UserIdentifier',
            ])
            ->add('materials', EntityType::class, [
                'label' => 'Trier les salles par équipement',
                'class' => Materials::class,
                'choice_label' => 'name',
                'mapped' => false,
                'multiple' => true,
                'expanded' => true,
                'required' => false,
                'attr' => [
                    'class' => 'materials-field materials-checkboxes'
                ],
                'row_attr' =>[
                    "id" => "row-materials"
                ],
                'label_attr' => [
                    'class' => 'label-materials',
                ],
            ])
            ->add('zoomswitch', CheckboxType::class, [
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
            ->add('zoomlink', UrlType::class, [
                'label' => "Lien pour la visio",
                'required' => false,
                'label_attr' => [
                    'class' => 'label-zoom',
                ],
            ])
            ->add('room', EntityType::class, [
                'class' => Room::class,
                'placeholder' => 'Choisir une salle pour le cours',
                'choice_label' => 'name',
                'required' => false,
                'label' => 'Salle',
                'attr' => [
                    'class' => 'room-select',
                ],
                'label_attr' => [
                    'class' => 'label-room',
                ],
            ])
            ->add('studentswitch', CheckboxType::class, [
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
                'placeholder' => "Choisissez la classe à laquelle vous faites cours",
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
                'choice_label' => 'UserIdentifier',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'students-field'
                ],
                'label_attr' => [
                    'class' => 'label-students',
                ],
            ])
            ->add('programme', EntityType::class, [
                'label' => 'Programmes',
                'class' => Programmes::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'programmes-field'
                ],
                'multiple' => true,
                'required' => false,
            ])
            ->add('lecons', EntityType::class, [
                'label' => 'Leçons',
                'placeholder' => "Souhaitez-vous ajouter une/des leçon(s) ?",
                'class' => Lecons::class,
                'choice_label' => 'nom',
                'attr' => [
                    'class' => 'lecons-field'
                ],
                'multiple' => true,
                'required' => false,
            ])
            ->add('reservedRooms', HiddenType::class, [
                'mapped' => false,
            ]);


        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if ($data instanceof Event && $data->getId() !== null) {
                // Pré-remplir les salles réservées si l'événement existe déjà
                $reservedRooms = $data->getRoom();
                if ($reservedRooms !== null) {
                    $form->get('reservedRooms')->setData($reservedRooms->getId());
                }
            }

            // Récupérer la date de début de l'événement
            $startDate = $data->getStart();
            $currentDateTime = new \DateTime();

            if ($startDate !== null && $startDate < $currentDateTime) {
                    $form->add('comment', TextareaType::class, [
                        'label' => 'Commentaire',
                        'required' => false,
                    ]);
            } else {
                // Si la date de début est future, ajoutez le champ "Objectif"
                $form->add('objectif', TextareaType::class, [
                    'label' => 'Objectif de la Séance et Travail à Faire',
                    'required' => false,
                ]);
            }

            // Vérifiez si l'événement est une récurrence ou a un parentEventId
            $isRecurrence = $data->getRecurrence() == 1 || $data->getParentEvent() !== null;

            // Ajoutez le champ modificationScope uniquement si c'est une récurrence
            if ($isRecurrence) {
                $form->add('modificationScope', ChoiceType::class, [
                    'label' => 'Portée de la modification',
                    'choices' => [
                        'Cet événement' => 'this_event',
                        'Tous les événements de la récurrence' => 'all_events',
                        'Tous les événements futurs de la récurrence' => 'future_events',
                    ],
                    'expanded' => true,
                    'required' => true,
                    'mapped' => false,
                ]);
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