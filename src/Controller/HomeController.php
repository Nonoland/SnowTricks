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
}
