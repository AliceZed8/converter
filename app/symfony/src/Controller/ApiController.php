<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#подключаем репозиторий с котировками
use App\Repository\QuotesRepository;
use App\Entity\Quotes;

class ApiController extends AbstractController
{
    #[Route('/api', name: 'app_api')]
    public function index(): Response
    {
        return $this->render('api/index.html.twig', [
            'controller_name' => 'ApiController',
        ]);
    }



    #[Route('/api/get_quotes', name: 'get_quotes', methods: ['GET'])]
    public function get_quotes(Request $request, QuotesRepository $quotes): Response
    {
        $qs = $quotes->get_all_quotes();
        $res = [];
        foreach ($qs as $q) {
            $res[] = $q->getCurrency();
        }
        return new Response(json_encode($res));
    }

    #[Route('/api/add_quote', name: 'add_quote', methods: ['POST'])]
    public function add_quote_(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currency = $content["currency"];
            $rate = $content["rate"];

            

            $ok = $quotes->add_quote($currency, $rate);

            $result = ["status" => "ok"];
            return new Response(json_encode($result));

            
        } catch (\Exception $e) {
            $result = ["status"=> "error"];
            return new Response(json_encode($result)); 
        
        }
        
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
