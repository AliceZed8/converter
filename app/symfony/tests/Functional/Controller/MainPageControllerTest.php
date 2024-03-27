<?php

namespace App\Tests\Functional\Controller\MainPageController;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainPageControllerTest extends WebTestCase {

    public function testMainPage(): void {
        $client = static::createClient();
        $client->request("GET","/");

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains("h2", "Currency Converter");
    }


}
