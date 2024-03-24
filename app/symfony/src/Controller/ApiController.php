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


    /*
        Для получения котировок GET

        curl -X GET localhost:8080/api/get_quotes
        
        -> ["EUR", "USD", ...]
    */
    #[Route('/api/get_quotes', name: 'get_quotes', methods: ['GET'])]
    public function get_quotes(Request $request, QuotesRepository $quotes): Response
    {
        
        return new Response(json_encode($quotes->get_all_quotes()));
    }

    /*
        Для добавления котировки

        curl -X POST localhost:8080/api/add_quote -d '{"currency":"USD", "rate":1.09}'

        -> {"status": "..."}
    */
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


    #[Route('/api/get_exchange_rate', name: 'get_exchange_rate', methods: ['POST'])]
    public function get_exchange_rate(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $from = $content["from"];
            $to = $content["to"];
            $amount = $content["amount"];

            $result = ["status" => "ok", "exchange_rate" => 1];
            return new Response(json_encode($result));
            

        } catch (\Exception $e) {
            $result = ["status" => "error"];
            return new Response(json_encode($result));
        }
        
    }
}
