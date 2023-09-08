<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    #[Route('/ajaxGetTricks', name: 'app_home_ajax_get_tricks')]
    public function getTricks(EntityManagerInterface $entityManager, Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $lastId = $request->get('lastId');
        $count = $request->get('count', 15);

        if (!$lastId) {
            $response->setContent(json_encode(['success' => false]));
            return $response;
        }

        $query = $entityManager->getRepository(Trick::class)->createQueryBuilder('t')
            ->where('t.id > :lastId')
            ->setParameter('lastId', $lastId)
            ->setMaxResults($count);

        $tricks = $query->getQuery()->getResult();

        $tricksHtml = [];
        foreach ($tricks as $trick) {
            $tricksHtml[] = $this->renderView('embed/card_trick.html.twig', [
                'trick' => $trick
            ]);
        }

        $response->setContent(json_encode(['success' => true, 'data' => $tricksHtml, 'targetCount' => $count]));

        return $response;
    }

    #[Route('/ajaxGetTrickComments', name: 'app_home_ajax_get_trick')]
    public function getTrickComments(EntityManagerInterface $entityManager, Request $request): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $lastId = $request->get('lastId');
        $count = $request->get('count', 15);

        if (!$lastId) {
            $response->setContent(json_encode(['success' => false]));
            return $response;
        }

        $query = $entityManager->getRepository(Comment::class)->createQueryBuilder('c')
            ->where('c.id > :lastId')
            ->setParameter('lastId', $lastId)
            ->setMaxResults($count);

        $comments = $query->getQuery()->getResult();

        $commentsHtml = [];
        foreach ($comments as $comment) {
            $commentsHtml[] = $this->renderView('embed/comment.html.twig', [
                'comment' => $comment
            ]);
        }

        $response->setContent(json_encode(['success' => true, 'data' => $commentsHtml, 'targetCount' => $count]));

        return $response;
    }
}
