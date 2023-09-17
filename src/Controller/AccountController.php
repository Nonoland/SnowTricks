<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
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
            $picturesFolder = $this->getParameter('uploads_base_url') . "/users";

            /** @var SubmitButton $removeProfilePicture */
            if ($form->has('submit_remove_profile_picture')) {
                $removeProfilePicture = $form->get('submit_remove_profile_picture');
                if ($removeProfilePicture->isSubmitted() && $removeProfilePicture->isClicked() && !$user->profilePictureIsDefault()) {
                    unlink($picturesFolder."/".$user->getProfilPicture());
                    $user->setProfilPicture("");

                    $entityManager->persist($user);
                    $entityManager->flush();

                    $this->addFlash(
                        'success',
                        'Your profile photo has been removed'
                    );
                }
            }

            $uploadedProfilePicture = $form->get('profil_picture')->getData();
            if ($uploadedProfilePicture) {
                $newFilename = $user->getUserIdentifier() . '.' . $uploadedProfilePicture->guessExtension();

                try {
                    $uploadedProfilePicture->move(
                        $picturesFolder,
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

                $this->addFlash(
                    'success',
                    'Your profile photo has been updated'
                );
            }

            return $this->redirectToRoute('app_account');
        }

        return $this->render('account/index.html.twig', [
            'form' => $form,
            'user_profile_picture' => $user->getProfilPicture()
        ]);
    }
}
