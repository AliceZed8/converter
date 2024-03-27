<?php


namespace App\Tests\Functional\Quotes\Repository;
use App\Entity\Quotes;
use App\Repository\QuotesRepository;


use Doctrine\ORM\EntityManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class QuotesRepositoryTest extends WebTestCase
{

    private QuotesRepository $repository;
    private Generator $faker;


    public function setUp(): void {
        parent::setUp();
        $this->repository = static::getContainer()->get(QuotesRepository::class);
        $this->faker = Factory::create();
    }


    public function test_quote_add_get_successfully(): void {
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);

        $quote = new Quotes();
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        
        $this->repository->add($quote);

        
        $existing_quote = $this->repository->get($currency);
        $this->assertEquals($quote->getCurrency(), $existing_quote->getCurrency());
        $this->assertEquals($quote->getRate(), $existing_quote->getRate());
    }


    public function test_quote_removed_success(): void
    {   
        $quote = new Quotes();
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $this->repository->add($quote);
        $this->repository->remove($currency);

        $existing_quote = $this->repository->get($currency);

        $this->assertEquals($existing_quote, null);
    }

    public function test_quote_updated_successfully(): void
    {
        $quote = new Quotes();
        $currency = $this->faker->currencyCode();
        $rate = $this->faker->randomFloat(2, 1, 50);
        $quote->setCurrency($currency);
        $quote->setRate($rate);

        $this->repository->add($quote);

        $new_rate = $this->faker->randomFloat(2, 1, 50);
        $quote->setRate($new_rate);
        $this->repository->update($currency, $new_rate);

        $updated_quote = $this->repository->get($currency);

        $this->assertEquals($updated_quote->getCurrency(), $quote->getCurrency());
        $this->assertEquals($updated_quote->getRate(), $quote->getRate());

    }

    public function test_get_all_quotes(): void
    {
        $quotes = [];
        for ($i = 0; $i < 10; $i++) {
            $quote = new Quotes();
            $quote->setCurrency($this->faker->currencyCode());
            $quote->setRate($this->faker->randomFloat(2, 1, 50));
            $quotes[] = [
                "currency"=> $quote->getCurrency(),
                "rate" => $quote->getRate()
            ];

            $this->repository->add($quote);
        }


        $this->assertEquals($this->repository->get_all(), $quotes);

    }
}

