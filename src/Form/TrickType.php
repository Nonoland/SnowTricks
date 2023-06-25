<?php

namespace App\Form;

use App\Entity\TrickGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Trick name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Trick description'
            ])
            ->add('trickGroup', EntityType::class, [
                'class' => TrickGroup::class,
                'choice_label' => 'title',
                'label_html' => true,
                'label' => 'Trick Group <a href="#" data-bs-toggle="modal" data-bs-target="#addTrickGroup"><span class="badge text-bg-success">Add new group</span></a>'
            ])
            ->add('firstImage', FileType::class, [
                'label' => 'First Image',
                'multiple' => false,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'hidden' => ''
                ]
            ])
            ->add('image1', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('image2', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('image3', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('media1', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('media2', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('media3', FileType::class, [
                'attr' => [
                    'hidden' => ''
                ],
                'required' => false
            ])
            ->add('save', SubmitType::class)
        ;
    }
}
