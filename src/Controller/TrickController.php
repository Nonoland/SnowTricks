<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Form\CommentType;
use App\Form\TrickGroupType;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/tricks/details/{slug}_{id}', name: 'app_trick_details')]
    public function showTrick(Request $request, ?Trick $trick): Response
    {
        if (!$trick) {
            return $this->redirect('/');
        }

        if ($request->get('slug') != $trick->getSlug()) {
            return $this->redirectToRoute(
                'app_trick_details',
                ['id' => $trick->getId(), 'slug' => $trick->getSlug()]
            );
        }

        $formComment = $this->createForm(CommentType::class);
        $formComment->handleRequest($request);

        if ($formComment->isSubmitted() && $formComment->isValid()) {
            /** @var Comment $comment */
            $comment = $formComment->getData();
            $comment->setTrick($trick);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();
        }

        return $this->render('trick/index.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'formComment' => $formComment
        ]);
    }

    #[Route('/tricks/edit/{id}', name: 'app_trick_edit')]
    public function editTrick(Request $request, ?Trick $trick): Response
    {
        if (!$trick) {
            return $this->redirect('/');
        }

        $formTrick = $this->createForm(TrickType::class, $trick);
        $formTrickGroup = $this->createForm(TrickGroupType::class);

        $formTrick->handleRequest($request);

        if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            $trick->setTitle($formTrick->get('title')->getData());
            $trick->setDescription($formTrick->get('description')->getData());

            $trick->setTrickGroup($formTrick->get('trickGroup')->getData());

            $uploadedFirstImage = $formTrick->get('firstImage')->getData();
            if ($uploadedFirstImage) {
                $newFilename = uniqid().'.'.$uploadedFirstImage->guessExtension();

                try {
                    $appPath = $this->getParameter('uploads_base_url');
                    $uploadedFirstImage->move(
                        $appPath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    //TODO : Changer
                    echo $e->getMessage();
                    die();
                }

                $trick->setFirstImage($newFilename);
            }

            $images = $trick->getImages();
            for ($i = 1; $i <= 3; $i++) {
                $uploadedFileImage = $formTrick->get('image'.$i)->getData();
                if ($uploadedFileImage) {
                    $newFilename = uniqid().'.'.$uploadedFileImage->guessExtension();

                    try {
                        $appPath = $this->getParameter('uploads_base_url');
                        $uploadedFileImage->move(
                            $appPath,
                            $newFilename
                        );
                    } catch (FileException $e) {
                        //TODO : Changer
                        echo $e->getMessage();
                        die();
                    }

                    $images[$i-1] = $newFilename;
                }
            }

            $medias = $trick->getMedias();
            for ($i = 1; $i <= 3; $i++) {
                $mediaInput = $formTrick->get('media'.$i)->getData();
                if ($mediaInput) {
                    $medias[$i-1] = $mediaInput;
                }
            }

            $trick->setImages($images);
            $trick->setMedias($medias);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'formTrick' => $formTrick,
            'formTrickGroup' => $formTrickGroup
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

        $formTrick = $this->createForm(TrickType::class);
        $formTrickGroup = $this->createForm(TrickGroupType::class);

        $formTrick->handleRequest($request);
        if ($formTrick->isSubmitted() && $formTrick->isValid()) {
            $trick->setTitle($formTrick->get('title')->getData());
            $trick->setDescription($formTrick->get('description')->getData());

            $trick->setTrickGroup($formTrick->get('trickGroup')->getData());

            $uploadedFirstImage = $formTrick->get('firstImage')->getData();
            if ($uploadedFirstImage) {
                $newFilename = uniqid().'.'.$uploadedFirstImage->guessExtension();

                try {
                    $appPath = $this->getParameter('uploads_base_url');
                    $uploadedFirstImage->move(
                        $appPath,
                        $newFilename
                    );
                } catch (FileException $e) {
                    //TODO : Changer
                    echo $e->getMessage();
                    die();
                }

                $trick->setFirstImage($newFilename);
            }

            $images = json_decode($formTrick->get('images')->getData(), true);
            $embeds = json_decode($formTrick->get('embeds')->getData(), true);

            $trickImages = [];
            foreach ($images as $image) {
                $appPath = $this->getParameter('uploads_base_url');
                $decodedContent = base64_decode($image);

                $imgData = getimagesize($image);
                $mimeType = $imgData["mime"];
                $extension = explode('/', $mimeType)[1];
                $fileName = uniqid().".$extension";

                $currentImage = fopen($image, 'r');
                $newFile = fopen("$appPath/$fileName", 'w');

                stream_copy_to_stream($currentImage, $newFile);

                fclose($currentImage);
                fclose($newFile);

                $trickImages[] = $fileName;
            }

            $trick->setImages($trickImages);
            $trick->setMedias($embeds);

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_home');
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
