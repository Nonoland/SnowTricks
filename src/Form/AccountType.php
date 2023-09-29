<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
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
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => [
                    'class' => 'form-control'
                ],
                'data' => $currentUser->getEmail(),
                'required' => false
            ])
            ->add('current_password', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('new_password', PasswordType::class, [
                'label' => 'Nouveau mot de passe',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('repeat_new_password', PasswordType::class, [
                'label' => 'Répéter le nouveau mot de passe',
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Save',
                'attr' => [
                    'class' => 'form-control btn-primary rounded px-3'
                ]
            ]);

    }
}
