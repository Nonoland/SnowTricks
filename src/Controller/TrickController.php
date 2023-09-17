<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\TrickGroup;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\TrickGroupType;
use App\Form\TrickType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/trick_add', name: 'app_trick_add')]
    public function addTrickData(MailerInterface $mailer): Response
    {
        $trick = $this->entityManager->getRepository(Trick::class)->find(12);

        $userIds = [7, 9];

        for($i = 0; $i < 50; $i++) {
            $comment = new Comment();
            $comment->setUser($this->entityManager->getRepository(User::class)->find($userIds[array_rand($userIds)]));
            $comment->setTrick($trick);
            $comment->setDateAdd(new \DateTime());
            $comment->setMessage("Message : $i");
            $this->entityManager->persist($comment);
        }

        $this->entityManager->flush();

        return new Response('add data');
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
            $comment->setUser($this->getUser());

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

            //Remove images
            $appPath = $this->getParameter('uploads_base_url');

            $removeImages = explode(',', $formTrick->get('removeImages')->getData());
            foreach ($removeImages as $image) {
                if (!$image) {
                    continue;
                }
                unlink($appPath.'/'.$image);
            }
            $trick->setImages(array_diff($trick->getImages(), $removeImages));

            //Remove embeds
            $removeEmbeds = explode(',', $formTrick->get('removeEmbeds')->getData());
            $trick->setMedias(array_diff($trick->getMedias(), $removeEmbeds));

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
                    /*$cookieToast = self::createCookieToast("There was a problem downloading the main trick image");

                    $response = $this->redirectToRoute('app_home');
                    $response->headers->setCookie($cookieToast);

                    return $response;*/

                    $this->addFlash(
                        'danger',
                        'There was a problem downloading the main trick image.'
                    );

                    return $this->redirectToRoute('app_home');
                }

                $trick->setFirstImage($newFilename);
            }

            $images = json_decode($formTrick->get('images')->getData(), true) ?? [];
            $embeds = json_decode($formTrick->get('embeds')->getData(), true) ?? [];

            $trickImages = $trick->getImages();
            foreach ($images as $image) {
                $appPath = $this->getParameter('uploads_base_url');

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
            $trick->setMedias(array_merge($embeds, $trick->getMedias()));

            $this->entityManager->persist($trick);
            $this->entityManager->flush();

            /*$cookieToast = self::createCookieToast("The snowboard trick was updated");

            $response = $this->redirectToRoute('app_home');
            $response->headers->setCookie($cookieToast);

            return $response;*/

            $this->addFlash(
                'success',
                'The snowboard trick has been updated.'
            );

            return $this->redirectToRoute('app_home');
        }

        return $this->render('trick/new.html.twig', [
            'controller_name' => 'TrickController',
            'trick' => $trick,
            'formTrick' => $formTrick,
            'formTrickGroup' => $formTrickGroup,
            'actionType' => 'edit'
        ]);
    }

    #[Route('/tricks/delete/{id}', name: 'app_trick_delete')]
    public function deleteTrick(int $id): Response
    {
        $trick = $this->entityManager->getRepository(Trick::class)->find($id);

        if (!$trick) {
            return $this->redirect('/');
        }

        $comments = $trick->getComments();

        foreach ($comments as $comment) {
            $this->entityManager->remove($comment);
        }

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        $this->addFlash(
            'success',
            'The snowboard trick has been removed.'
        );

        return $this->redirectToRoute('app_home');
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
                    $this->addFlash(
                        'warning',
                        'There was a problem updating the snowboard trick'
                    );

                    return $this->redirectToRoute('app_home');
                }

                $trick->setFirstImage($newFilename);
            }

            $images = json_decode($formTrick->get('images')->getData(), true);
            $embeds = json_decode($formTrick->get('embeds')->getData(), true);

            $trickImages = [];
            foreach ($images as $image) {
                $appPath = $this->getParameter('uploads_base_url');

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

            $this->addFlash(
                'success',
                'The snowboard trick was created'
            );

            return $this->redirectToRoute('app_home');;
        }

        return $this->render('trick/new.html.twig', [
            'controller_name' => 'TrickController',
            'formTrick' => $formTrick,
            'formTrickGroup' => $formTrickGroup,
            'actionType' => 'add'
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
