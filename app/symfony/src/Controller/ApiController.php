<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#подключаем репозиторий с котировками
use App\Repository\QuotesRepository;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }



    #[Route('/api/get_quotes', name: 'get_quotes')]
    public function get_quotes(Request $request, QuotesRepository $quotes): Response
    {
        $qs = $quotes->get_all_quotes();
        return new Response(json_encode($qs));
    }

    #[Route('/api/add_quote', name: 'add_quote')]
    public function add_quote_(Request $request, QuotesRepository $quotes): Response
    {
        $currency = $request->request->get('currency');
        $rate = $request->request->get('rate');

        $ok = $quotes->add_quote($currency, $rate);

        $result = ["status" => "ok"];
        if ($ok == true) return new Response(json_encode($result));

        $result['status'] = "error";
        return new Response(json_encode($result));
    }


    #[Route('/api/get_exchange_rate', name: 'get_exchange_rate')]
    public function get_exchange_rate(Request $request, QuotesRepository $quotes): Response
    {
        $from = $request->request->get("from");
        $to = $request->request->get("to");
        $amount = $request->request->get("amount");
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }
}
