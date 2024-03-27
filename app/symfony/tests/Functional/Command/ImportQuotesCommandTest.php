<?php


namespace App\Tests\Functional\Command\ImportQuotesCommand;

use App\Repository\QuotesRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ImportQuotesTest extends KernelTestCase
{

    private QuotesRepository $quotesRepository;
    public function setUp(): void {
        parent::setUp();
        $this->quotesRepository = static::getContainer()->get(QuotesRepository::class);
    }

    public function testExecute()
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('app:import_quotes');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        
        
        $this->assertStringContainsString("Quotes imported", $output);
        $this->assertStringContainsString(json_encode($this->quotesRepository->get_all()), $output);



        //В случае ошибки импорта котировок
        $failcommandTester = new CommandTester($command);
        $failcommandTester->execute([
            "test_url" => "google.com"
        ]);

        $output = $failcommandTester->getDisplay();
        $this->assertStringContainsString("Failed to load quotes", $output);

    }
}