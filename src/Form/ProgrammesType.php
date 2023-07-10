<?php

namespace App\Form;

use App\Entity\Programmes;
use App\Entity\ProgrammesLecons;
use App\Entity\Users;
use App\Repository\UsersRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class ProgrammesType extends AbstractType
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
                'label' => 'Nom du programme'
            ])
            ->add('image', FileType::class, [
                'label' => 'Illustration',
                'mapped'=>false,
                'multiple'=>false,
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
            ->add('ProgrammesLecons', EntityType::class, [
                'class' => ProgrammesLecons::class,
                'choice_label'=>'name',
                'label'=>'Programme privé ou publique',
                'placeholder' => 'Choisir entre programme publique ou privé',
            ])
            ->add('users', EntityType::class, [
                'class' => Users::class,
                'choices' => $allusers,
                'multiple'=>true,
                'placeholder' => 'Élèves à qui partager',
                'required'=>false,
                'label'=>'Élèves',
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
            'data_class' => Programmes::class,
        ]);
    }
}
