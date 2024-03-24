<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\QuotesRepository;


class MainPageController extends AbstractController
{
    #[Route('/', name: 'app_main_page')]
    public function index(QuotesRepository $quotes): Response
    {
        return $this->render('main_page/index.html.twig', [
            'controller_name' => 'MainPageController',
            'quotes' => $quotes->get_all_quotes()
        ]);
    }
}
