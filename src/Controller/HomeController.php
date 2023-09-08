<?php

namespace App\Controller;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $trickRepository = $entityManager->getRepository(Trick::class);

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'tricks' => $trickRepository->findByLimit(15)
        ]);
    }

    #[Route('/ajaxGetTricks', name: 'app_home_ajax_get_trick')]
    public function getTrick(EntityManagerInterface $entityManager, Request $request): Response
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

        $response->setContent(json_encode(['success' => true, 'data' => $tricksHtml]));

        return $response;
    }
}
