<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

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

        $imagesOptions = [
            'required' => false,
            'mapped' => false,
        ];

        $mediasOptions = [
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

            $imagesOptions['attr']['data-images'] = json_encode($trick->getImages());
            $mediasOptions['attr']['data-embeds'] = json_encode($trick->getMedias());

            $builder->add('removeImages', HiddenType::class, [
                'mapped' => false
            ]);
            $builder->add('removeEmbeds', HiddenType::class, [
                'mapped' => false
            ]);
        }

        $builder->add('firstImage', FileType::class, $firstImageOptions);

        $builder->add("images", HiddenType::class, $imagesOptions);

        $builder->add("embeds", HiddenType::class, $mediasOptions);

        $builder
            ->add('save', SubmitType::class)
        ;
    }
}
