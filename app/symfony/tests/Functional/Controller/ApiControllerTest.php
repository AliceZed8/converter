<?php


namespace App\Tests\Functional\Controller\ApiController;


use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Quotes;
use App\Repository\QuotesRepository;

use Faker\Factory;
use Faker\Generator;


class ApiControllerTest extends ApiTestCase {

    private QuotesRepository $quotesRepository;
    private Generator $faker;

    
    public function setUp(): void {
        parent::setUp();
        $this->quotesRepository = static::getContainer()->get(QuotesRepository::class);
        $this->faker = Factory::create();
    }


    public function test_get_quotes(): void 
    {
        $client = static::createClient();

        $client->request("GET","/api/get_quotes");
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($json_response, $this->quotesRepository->get_all());

    }


    /*
        ADD QUOTE
    */


    public function  test_add_quote_failure(): void
    {
        $client = static::createClient();
        //1 Пустой запрос
        $client->request("POST","/api/add_quote");
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");

        //2 Неполный запрос
        $client->request("POST","/api/add_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                ]
            ]
        );
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");


        //3 Невалидный курс
        $client->request("POST","/api/add_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                    "rate" => 0.00
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");



    }


    public function test_add_quote_success(): void 
    {
        //Генерим котировку
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);

        $quote = new Quotes();
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $client = static::createClient();


        //Добавляем котировку по апи
        $client->request("POST","/api/add_quote",
            [
                "json" => [
                    "currency" => $currency,
                    "rate" => $rate
                ]
            ]
        );

        
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "ok");

        //Вытаскиваем котировку
        $added_quote = $this->quotesRepository->get($currency);
        
        //Сравниваем с добавленной котировкой
        $this->assertEquals($quote->getCurrency(), $added_quote->getCurrency());
        $this->assertEquals($quote->getRate(), $added_quote->getRate());

    }



    /*
        REMOVE QUOTE
    
    */
    public function test_remove_quote_success():void 
    {
        // ### 1 Просто добавляем
        //Генерим котировку
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);

        $quote = new Quotes();
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        //Добавляем ее
        $this->quotesRepository->add($quote);


        //Удаляем по апи
        $client = static::createClient();

        $client->request("POST","/api/remove_quote",
            [
                "json" => [
                    "currency" => $currency,
                ]
            ]
        );

        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чек статуса
        $this->assertEquals($json_response["status"], "ok");

        //Попробуем вытащить удаленную котировку
        $added_quote = $this->quotesRepository->get($currency);

        $this->assertEquals($added_quote, null);



        // ### Если котировка отсутсвует
        $client->request("POST","/api/remove_quote",
            [
                "json" => [
                    "currency" => $currency,
                ]
            ]
        );

        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чек 
        $this->assertEquals($json_response["status"], "error");
    }

    public function test_remove_quote_failure():void 
    {
        $client = static::createClient();

        //--- 1 Пустой запрос
        $client->request("POST","/api/remove_quote");
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");


        //--- 2 Неполный запрос
        $client->request("POST","/api/remove_quote",
            [
                "json" => [
                    "cur" => $this->faker->currencyCode(),
                ]
            ]
        );
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");


        //--- 3 Котировка отсутсвует
        $client->request("POST","/api/remove_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                ]
            ]
        );
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");
        
    }



    /*
        UPDATE QUOTE
    */

    public function test_update_quote_success(): void 
    {
        //Генерим котировку
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);

        $quote = new Quotes();
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        //Добавляем ее
        $this->quotesRepository->add($quote);

        $updated_rate = $this->faker->randomFloat(2, 1, 50);
        $quote->setRate($updated_rate);

        $client = static::createClient();

        //Обновляем котировку по апи
        $client->request("POST","/api/update_quote",
            [
                "json" => [
                    "currency" => $currency,
                    "rate" => $updated_rate
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чек статуса
        $this->assertEquals($json_response["status"], "ok");

        //Вытаскиваем котировку
        $added_quote = $this->quotesRepository->get($currency);
        
        //Сравниваем с добавленной котировкой
        $this->assertEquals($quote->getCurrency(), $added_quote->getCurrency());
        $this->assertEquals($quote->getRate(), $added_quote->getRate());

    }

    public function test_update_quote_failure(): void
    {
        $client = static::createClient();
        //1 Пустой запрос
        $client->request("POST","/api/update_quote");
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");

        //2 Неполный запрос
        $client->request("POST","/api/update_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                ]
            ]
        );
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");


        //3 Невалидный курс
        $client->request("POST","/api/update_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                    "rate" => 0.00
                ]
            ]
        );

        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");

        //4 Котировка отсутсвует
        $client->request("POST","/api/remove_quote",
            [
                "json" => [
                    "currency" => $this->faker->currencyCode(),
                    "rate" => 1
                ]
            ]
        );
        $this->assertResponseIsSuccessful();

        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");

    }


    /*
        GET EXCHANGE RATE
    
    */

    public function test_get_exchange_rate_success(): void
    {

        //Генерим котировки
        $currency_from = $this->faker->currencyCode();
        $rate_from = $this->faker->randomFloat(2, 1,50);
        $from_quote = new Quotes();
        $from_quote->setCurrency($currency_from);
        $from_quote->setRate($rate_from);

        $currency_to = $this->faker->currencyCode();
        $rate_to = $this->faker->randomFloat(2, 1,50);
        $to_quote = new Quotes();
        $to_quote->setCurrency($currency_to);
        $to_quote->setRate($rate_to);

        //Добавляем в бд
        $this->quotesRepository->add($from_quote);
        $this->quotesRepository->add($to_quote);

        //Получаем курс по апи
        $client = static::createClient();
        $client->request("POST","/api/get_exchange_rate",
            [
                "json" => [
                    "from" => $currency_from,
                    "to" => $currency_to
                ]
            ]
        );
        
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чек статуса
        $this->assertEquals($json_response["status"], "ok");

        //Чек курса
        $this->assertEquals($json_response["exchange_rate"], $rate_to / $rate_from);

    }

    public function test_get_exchange_rate_failure():void 
    {
        $client = static::createClient();
        //1 Пустой запрос
        $client->request("POST","/api/get_exchange_rate");
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);

        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");

        
        //2 Неполный запрос
        $client->request("POST","/api/get_exchange_rate",
            [
                "json" => [
                    "from" => $this->faker->currencyCode(),
                ]
            ]
        );
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);
        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");


        //3 Отсутсвуют котировка (-ки)

        $client->request("POST","/api/get_exchange_rate",
            [
                "json" => [
                    "from" => $this->faker->currencyCode(),
                    "to" => $this->faker->currencyCode()
                ]
            ]
        );
        $this->assertResponseIsSuccessful();
        $json_response = json_decode($client->getResponse()->getContent(), true);


        //Чекаем статус
        $this->assertEquals($json_response["status"], "error");
       
    }
}