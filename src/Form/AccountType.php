<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AccountType extends AbstractType
{

    private TokenStorageInterface $tokenStorage;
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();

        $builder
            ->add('profil_picture', FileType::class, [
                'required' => false
            ]);

        if (!$currentUser->profilePictureIsDefault()) {
            $builder
                ->add('submit_remove_profile_picture', SubmitType::class, [
                    'label' => 'Delete profile picture',
                    'attr' => [
                        'class' => 'form-control btn-primary rounded px-3'
                    ]
                ]);
        }

        $builder
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'form-control btn-primary rounded px-3'
                ]
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
