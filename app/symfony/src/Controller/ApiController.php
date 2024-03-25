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


            if ($quotes->quote_exists($currency)) {
                return new Response(json_encode(["status" => "error"]));
            }

            $quotes->add_quote($currency, $rate);
            return new Response(json_encode(["status" => "ok"]));

            
        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error"])); 
        
        }
        
    }

    /*
        Для удаления котировки

        curl -X POST localhost:8080/api/remove_quote -d '{"currency":"USD"}'

        -> {"status": "..."}
    */
    #[Route('/api/remove_quote', name: 'remove_quote', methods: ['POST'])]
    public function remove_quote_(Request $request, QuotesRepository $quotes): Response
    {
        try {
            $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $currency = $content["currency"];
            if (!$quotes->remove_quote($currency)) {
                return new Response(json_encode(["status"=> "error"]));
            }

            return new Response(json_encode(["status"=> "ok"]));

            
        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error"])); 
        
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

            //если котировка есть в бд
            if ($quotes->update_quote($currency, $rate)) {
                return new Response(json_encode(["status"=> "ok"]));
            }

            return new Response(json_encode(["status" => "error"]));       
            
            
        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error"])); 
        
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


            $from = $request->request->get("from");
            $to = $request->request->get("to");

            $from = $content["from"];
            $to = $content["to"];

            if ("EUR" === $from) {
                $quote = $quotes->get_quote($to);

                //если котировки нет
                if (is_null($quote)) {
                    return new Response(json_encode(["status"=> "error"]));
                }
                
                $rate = $quote->getRate();
                return new Response(json_encode([
                    "status"=> "ok",
                    "exchange_rate" => $rate
                ]));

            } 

            //кросс курс
            $from_quote = $quotes->get_quote($from);
            $to_quote = $quotes->get_quote($to);

            //Если отсутствуют котировки
            if (is_null($from_quote) || is_null($to_quote)) {
                return new Response(json_encode(["status"=> "error"]));
            }

            $rate = $to_quote->getRate() / $from_quote->getRate();
            return new Response(json_encode([
                "status"=> "ok",
                "exchange_rate" => $rate
            ]));
            

        } catch (\Exception $e) {
            return new Response(json_encode(["status" => "error"]));
        }
        
    }
}
