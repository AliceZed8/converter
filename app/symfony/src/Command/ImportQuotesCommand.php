<?php

namespace App\Command;

use App\Entity\Quotes;
use Doctrine\Persistence\ManagerRegistry;
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
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct();

        $this->managerRegistry = $managerRegistry;
    }

    protected function configure(): void
    {
        $this
            ->addOption("update", null, InputOption::VALUE_OPTIONAL, "update quotes in database")
        ;
    }

    protected function load_quotes(): array{
        $quotes = [];
        $result = file_get_contents("https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
        $xml = simplexml_load_string($result);
        
        
        foreach($xml->Cube->Cube->Cube as $Cube) {
            $quotes[] = [
                "currency" => (string) $Cube->attributes()->currency,
                "rate" => (float) $Cube->attributes()->rate
            ];
            unset($Cube);
        }

        
        return $quotes;
    }


    protected function addQuotes(array $quotes): void {
        $manager = $this->managerRegistry->getManager();

        foreach ($quotes as $q) {
            $quote = new Quotes();
            $quote->setCurrency($q["currency"]);
            $quote->setRate($q["rate"]);

            $manager->persist($quote);
            $manager->flush();
        }
    }

    protected function updateQuotes(array $quotes): void {
        $rep = $this->managerRegistry->getManager()->getRepository(Quotes::class);
        foreach ($quotes as $q) {
            $quote = $rep->findOneBy(["currency" => $q["currency"]]);
            $quote->setRate($q["rate"]);

            $rep->persist($quote);
            $rep->flush();
        }
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title("Loading quotes from ecb.europa.eu");

        $update_opt = $input->getOption("update");

        $quotes = [];
        try {
            $quotes = $this->load_quotes();
        } catch(\Exception $e) {
            $io->error($e->getMessage());
            $io->error("Failed to load quotes");
            return Command::FAILURE;
        }
        
        $quotes[] = [
            "currency" => "EUR",
            "rate" => 1.0
        ];

        if ("true" === $update_opt) {
            $this->updateQuotes($quotes);
            $io->success("Quotes updated");

            return Command::SUCCESS;
        }
            

        $this->addQuotes($quotes);
        $io->success("Quotes added");
        
        return Command::SUCCESS;
    }
}
