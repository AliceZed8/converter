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
    // #[Route('/api', name: 'app_api')]
    // public function index(): Response
    // {
    //     return $this->render('api/index.html.twig', [
    //         'controller_name' => 'ApiController',
    //     ]);
    // }


    /*
        Для получения котировок GET

        curl -X GET localhost:8080/api/get_quotes
        
        -> ["EUR", "USD", ...]
    */
    #[Route('/api/get_quotes', name: 'get_quotes', methods: ['GET'])]
    public function get_quotes(Request $request, QuotesRepository $quotes): Response
    {
        
        return new Response(json_encode($quotes->get_all()));
    }

    /*
        Для добавления котировки

        curl -X POST localhost:8080/api/add_quote -d '{"currency":"USD", "rate":1.09}'

        -> {"status": "..."}
    */
    #[Route('/api/add_quote', name: 'add_quote', methods: ['POST'])]
    public function add_quote(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currency = $content["currency"];
            $rate = $content["rate"];

            if ($rate == 0) throw new \Exception("invalid rate");

            $quote = new Quotes();
            $quote->setCurrency($currency);
            $quote->setRate($rate);

            if (!is_null($quotes->get($currency))) throw new \Exception("quote exists");

            $quotes->add($quote);
            return new Response(json_encode(["status" => "ok"]));

            
        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error", "msg" => $e->getMessage()])); 
        
        }
        
    }

    /*
        Для удаления котировки

        curl -X POST localhost:8080/api/remove_quote -d '{"currency":"USD"}'

        -> {"status": "..."}
    */
    #[Route('/api/remove_quote', name: 'remove_quote', methods: ['POST'])]
    public function remove_quote(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currency = $content["currency"];
            

            if (is_null($quotes->get($currency))) throw new \Exception("quote not exists");

            $quotes->remove($currency);
            return new Response(json_encode(["status"=> "ok"]));

        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error", "msg" => $e->getMessage()])); 
        
        }
        
    }




    /*
        Для обновления котировки

        curl -X POST localhost:8080/api/update_quote -d '{"currency":"USD", "rate":1.09}'

        -> {"status": "..."}
    */
    #[Route('/api/update_quote', name: 'update_quote', methods: ['POST'])]
    public function update_quote_(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currency = $content["currency"];
            $rate = $content["rate"];
            if ($rate == 0) throw new \Exception("invalid rate");

            if (is_null($quotes->get($currency))) throw new \Exception("quote not exists");

            //если котировка есть в бд
            $quotes->update($currency, $rate);
            return new Response(json_encode(["status"=> "ok"]));

        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error", "msg" => $e->getMessage()])); 
        
        }
        
    }

    /*
        Для получения курса обмена

        curl -X POST localhost:8080/api/get_exchange_rate -d '{"from":"USD", "to": "EUR"}'

        -> {"status": "...", "exchange_rate": ...}
    */


    #[Route('/api/get_exchange_rate', name: 'get_exchange_rate', methods: ['POST'])]
    public function get_exchange_rate(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
            $from = $content["from"];
            $to = $content["to"];

            $from_quote = $quotes->get($from);
            $to_quote = $quotes->get($to);

            //Если отсутствуют котировки
            if (is_null($from_quote) || is_null($to_quote)) throw new \Exception("quote or quotes not exists");

            $rate = $to_quote->getRate() / $from_quote->getRate();
            return new Response(json_encode([
                "status"=> "ok",
                "exchange_rate" => $rate
            ]));
            

        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error", "msg"=> $e->getMessage()]));
        }
        
    }
}
