<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Form\TrickGroupType;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tricks/details/{slug}_{id}', name: 'app_trick_details')]
    public function showTrick(string $slug, int $id): Response
    {
        $trick = $this->entityManager->getRepository(Trick::class)->find($id);

        if (!$trick) {
            return $this->redirect('/');
        }

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick
        ]);
    }

    #[Route('/tricks/edit/{id}', name: 'app_trick_edit')]
    public function editTrick(int $id): Response
    {
        $trick = $this->entityManager->getRepository(Trick::class)->find($id);

        if (!$trick) {
            return $this->redirect('/');
        }

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick
        ]);
    }

    #[Route('/tricks/delete/{id}', name: 'app_trick_delete')]
    public function deleteTrick(int $id): Response
    {
        $trick = $this->entityManager->getRepository(Trick::class)->find($id);

        if (!$trick) {
            return $this->redirect('/');
        }

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $this->redirect('/');
    }

    #[Route('/tricks/new', name: 'app_trick_new')]
    public function newTrick(Request $request): Response
    {
        $trick = new Trick();

        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrickGroup = $this->createForm(TrickGroupType::class);

        $formTrick->handleRequest($request);
        if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            /** @var Trick $trick */
            $trick = $formTrick->getData();

            $uploadedFiles = $formTrick->get('trickImageMedia')->getData();

            /** @var UploadedFile $uploadedFile */
            foreach ($uploadedFiles as $uploadedFile) {
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = uniqid().'.'.$uploadedFile->guessExtension();

                try {
                    $appPath = $this->getParameter('uploads_base_url');
                    $uploadedFile->move(
                        $appPath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    echo $e->getMessage();
                    die();
                }

                $trick->addImage($newFilename);
            }

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            $response = new Response();
            $response->setContent(json_encode(['success' => true]));
            return $response;
        }

        return $this->render('trick/new.html.twig', [
            'controller_name' => 'TrickController',
            'formTrick' => $formTrick,
            'formTrickGroup' => $formTrickGroup
        ]);
    }

    #[Route('/trickGroup/new', name: 'app_trick_new_trick_group')]
    public function newTrickGroup(Request $request): Response
    {
        $trickGroup = new TrickGroup();

        $formTricksGroup = $this->createForm(TrickGroupType::class, $trickGroup);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $formTricksGroup->handleRequest($request);
        if ($formTricksGroup->isSubmitted() && $formTricksGroup->isValid()) {
            $trickGroup = $formTricksGroup->getData();

            $this->entityManager->persist($trickGroup);
            $this->entityManager->flush();

            $response->setContent(json_encode(['success' => true, 'id' => $trickGroup->getId()]));
            return $response;
        }

        $response->setContent(json_encode(['success' => false]));
        return $response;
    }

    #[Route('/trick/verifyName/{title}', name: 'app_trick_verify_slug')]
    public function verifyName(EntityManagerInterface $entityManager, string $title): Response
    {
        $trickRepository = $entityManager->getRepository(Trick::class);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $tricks = $trickRepository->findBy([
            'title' => $title
        ]);

        if (!empty($tricks)) {
            $response->setContent(json_encode(['success' => false]));
        } else {
            $response->setContent(json_encode(['success' => true]));
        }

        return $response;
    }
}
