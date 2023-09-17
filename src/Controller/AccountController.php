<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(AccountType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedProfilePicture = $form->get('profil_picture')->getData();
            if ($uploadedProfilePicture) {
                $newFilename = $user->getUserIdentifier() . '.' . $uploadedProfilePicture->guessExtension();

                try {
                    $appPath = $this->getParameter('uploads_base_url') . "/users";
                    $uploadedProfilePicture->move(
                        $appPath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'warning',
                        'There was a problem updating your profile photo'
                    );

                    return $this->redirectToRoute('app_home');
                }

                $user->setProfilPicture($newFilename);

                $entityManager->persist($user);
                $entityManager->flush();
            }

            $this->addFlash(
                'success',
                'Your profile photo has been updated'
            );

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form,
            'user_profile_picture' => $user->getProfilPicture()
        ]);
    }
}
