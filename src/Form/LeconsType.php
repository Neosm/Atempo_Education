<?php

namespace App\Form;

use App\Entity\Lecons;
use App\Entity\Programmes;
use App\Entity\ProgrammesLecons;
use App\Entity\Users;
use App\Repository\UsersRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LeconsType extends AbstractType
{
    private $usersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->usersRepository = $usersRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $allusers = $this->usersRepository->findAllStudent();
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la leçon'
            ])
            ->add('users', EntityType::class, [
                'class' => Users::class,
                'choices' => $allusers,
                'multiple'=>true,
                'placeholder' => 'Élèves à qui partager',
                'required'=>false,
                'label'=>'Élèves',
            ])
            ->add('ProgrammesLecons', EntityType::class, [
                'class' => ProgrammesLecons::class,
                'choice_label'=>'name',
                'label'=>'Leçons privée ou publique',
                'placeholder' => 'Choisir entre une leçon publique ou privée',
            ])
            ->add('programmes', EntityType::class, [
                'class' => Programmes::class,
                'required' =>false,
                'placeholder' => 'Choisir un programme',
                'label' => 'Programme',
                'choice_label'=>'nom',
            ])
            ->add('thumbnails', FileType::class, [
                'label' => 'Illustration',
                'mapped'=>false,
                'multiple'=>false,
                'required' => $options['required'],
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
            ->add('contenu', CKEditorType::class, [
                'label' => 'Contenu de la leçon'
            ])
            ->add('pdf', FileType::class, [
                'label' => 'Cours | Fichier PDF',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '130M',
                        'maxSizeMessage'=>'Fichier trop lourd, poids maximum : 130 Méga-Octet, veuillez choisir un fichier plus petit',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez téléverser un fichier valide s\'il vous plait',
                    ])
                ],
                'attr' => [
                    'accept'=>'.pdf'
                ]
            ])
            ->add('audio', UrlType::class, [
                'required'=>false,
                'label' => "Audio | Lien URL du son"
            ])
            ->add('video', UrlType::class, [
                'required'=>false,
                'label' => "Vidéo | Lien URL de la vidéo"
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
            'data_class' => Lecons::class,

        ]);
    }
}
