<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Mime\Part\File;

class TrickType extends AbstractType
{

    private $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = "/$uploadDir/";
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Trick $trick */
        $trick = $builder->getData();

        $firstImageOptions = [
            'label' => 'First Image',
            'multiple' => false,
            'mapped' => false,
            'attr' => [
                'accept' => 'image/*',
                'hidden' => ''
            ],
            'required' => false
        ];

        $imageOptions = [
            'attr' => [
                'accept' => 'image/*',
                'hidden' => ''
            ],
            'required' => false,
            'mapped' => false,
            'multiple' => false
        ];

        $mediaOptions = [
            'attr' => [
                'hidden' => ''
            ],
            'required' => false,
            'mapped' => false
        ];

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
            ]);

        if ($trick) {
            $firstImageOptions['attr']['data-edit'] = $this->uploadDir.$trick->getFirstImage();
        }

        $builder->add('firstImage', FileType::class, $firstImageOptions);

        for($i = 1; $i <= 3; $i++) {
            $cloneImageOptions = $imageOptions;
            $cloneMediaOptions = $mediaOptions;

            if ($trick) {
                if ($i <= count($trick->getImages())) {
                    $cloneImageOptions['attr']['data-edit'] = $this->uploadDir.$trick->getImages()[$i-1];
                }

                if ($i <= count($trick->getMedias())) {
                    $cloneMediaOptions['attr']['data-edit'] = $trick->getMedias()[$i-1];
                }
            }

            $builder->add("image$i", FileType::class, $cloneImageOptions);
            $builder->add("media$i", TextType::class, $cloneMediaOptions);
        }

        $builder
            ->add('save', SubmitType::class)
        ;
    }
}
