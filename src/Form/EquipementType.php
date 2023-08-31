<?php

namespace App\Form;

use App\Entity\Materials;
use App\Entity\Room;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EquipementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la salle',
            ])
            ->add('rooms', EntityType::class, [
                'label' => 'Ajouter des salles pour affilié l\'équipement à la salle',
                'class' => Room::class,
                'choice_label' => 'name',
                'multiple' => true,
                'required' => false,
                'attr' => [
                    'class' => 'room-field'
                ],
                'label_attr' => [
                    'class' => 'label-room',
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
            'data_class' => Materials::class,
        ]);
    }
}
