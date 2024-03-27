<?php

namespace App\Command;

use App\Entity\Quotes;
use App\Repository\QuotesRepository;

use Doctrine\Persistence\ManagerRegistry;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


#[AsCommand(
    name: 'app:import_quotes',
    description: 'import quotes from https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml',
)]
class ImportQuotesCommand extends Command
{   
    private QuotesRepository $quotesRepository;

    public function __construct(QuotesRepository $quotesRepository)
    {
        parent::__construct();

        $this->quotesRepository = $quotesRepository;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('test_url', InputArgument::OPTIONAL, 'some test url', "none")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        //Для тестирования
        $default_uri = "https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml";
        $test_url = $input->getArgument("test_url");
        $url =  ("none" === $test_url) ?  $default_uri : $test_url;

        $io->title("Loading quotes from $url");
        $quotes = [];

        try {
            //Парсим котировки
            $result = file_get_contents($url);
            $xml = simplexml_load_string($result);
            foreach($xml->Cube->Cube->Cube as $Cube) {
                $quotes[] = [
                    "currency" => (string) $Cube->attributes()->currency,
                    "rate" => (float) $Cube->attributes()->rate
                ];
                unset($Cube);
            }
            
            if (count($quotes) === 0) throw new RuntimeException();

            $quotes[] = [
                "currency" => "EUR",
                "rate" => 1.0
            ]; 
            
            
        } catch(\Exception $e) {
            $io->error("Failed to load quotes");
            return Command::FAILURE;
        }


        foreach ($quotes as $q) {
            $quote = new Quotes();
            $quote->setCurrency($q["currency"]);
            $quote->setRate($q["rate"]);
            $this->quotesRepository->add($quote);
        }

        $output->write(json_encode($quotes));

        $io->success("Quotes imported");
    
        return Command::SUCCESS;
    }
}
