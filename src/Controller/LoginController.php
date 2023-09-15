<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ReinitPasswordType;
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

        if ($error) {
            $this->addFlash(
                'danger',
                'The username or password is incorrect'
            );
        }

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername
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
                'danger',
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

    #[Route('/forgotPassword', name: 'app_demand_forgot_password')]
    public function demandForgotPassword(Request $request, EntityManagerInterface $entityManager, EmailService $emailService)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $form->get('email')->getData()]);

            if ($user) {
                $user->setResetPasswordToken(bin2hex(random_bytes(64)));
                $user->setValid(false);

                $entityManager->persist($user);
                $entityManager->flush();

                $emailService->sendResetPasswordEmail($user);
            }

            $this->addFlash(
                'success',
                'If an account is associated with this email, you will receive an email to reset your password.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/forgot_password.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/reinitPassword/{token}', name: 'app_reinit_password')]
    public function reinitPassword(String $token, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['resetPasswordToken' => $token, 'valid' => 0]);

        if (!$user) {
            $this->addFlash(
                'danger',
                'The password reset token is incorrect. Please try again.'
            );

            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ReinitPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('password')->getData() !== $form->get('repeat_password')->getData()) {
                $this->addFlash(
                    'warning',
                    'The password and confirmation password do not match. Please try again.'
                );

                return $this->redirectToRoute('app_reinit_password', ['token' => $token]);
            }

            $user->setPassword($form->get('password')->getData());
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            $user->setValid(true);
            $user->setResetPasswordToken('');

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your password has been reset. You can now log in with your new password.'
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render('login/reinit_password.html.twig', [
            'form' => $form
        ]);
    }
}
