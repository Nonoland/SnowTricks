<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $security->logout(false);

        return $this->redirectToRoute('app_login');
    }

    #[Route('/register', name: 'app_register')]
    public function register(EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher, EmailService $emailService): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() !== $form->get('repeat_password')->getData()) {
                $this->addFlash(
                    'warning',
                    'The password and confirmation password do not match. Please try to register again.'
                );

                return $this->redirectToRoute('app_register');
            }

            /** @var User $user */
            $user = $form->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $user->setRegistrationToken(bin2hex(random_bytes(64)));
            $user->setValid(false);

            $entityManager->persist($user);
            $entityManager->flush();

            /* Send a validation email */
            $emailService->sendRegistrationEmail($user);

            $this->addFlash(
                'success',
                'Your registration has been successful. You will shortly receive a validation email!'
            );
        }

        return $this->render('login/register.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/accountValidation/{token}', name: 'app_validation')]
    public function validation(String $token, EntityManagerInterface $entityManager)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['registrationToken' => $token, 'valid' => 0]);

        if (!$user) {
            $this->addFlash(
                'error',
                'Account validation link is invalid. Contact the site administrator to resolve this problem.'
            );

            return $this->redirectToRoute('app_home');
        }

        $user->setValid(true);
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Your account is now activated. You can now log in!'
        );

        return $this->redirectToRoute('app_login');
    }
}
