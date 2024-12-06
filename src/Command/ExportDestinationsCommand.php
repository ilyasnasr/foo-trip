<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(
    name: 'app:export-destinations',
    description: 'Export destinations from API',
)]
class ExportDestinationsCommand extends Command
{

    private HttpClientInterface $client;
    private ParameterBagInterface $params;

    public function __construct(HttpClientInterface $client, ParameterBagInterface $params)
    {
        parent::__construct();
        $this->client = $client;
        $this->params = $params;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $appHost = $this->params->get('app.host');

        $apiUrl = $appHost . '/api/destinations';

        $response = $this->client->request('GET', $apiUrl);
        $destinations = $response->toArray();

        if (empty($destinations)) {
            $output->writeln('<error>No destinations found!</error>');
            return Command::FAILURE;
        }

        $csvFile = fopen('destinations.csv', 'w');
        if ($csvFile === false) {
            $output->writeln('<error>Failed to create CSV file.</error>');
            return Command::FAILURE;
        }

        fputcsv($csvFile, ['Name', 'Description', 'Price', 'Duration', 'Image URL']);

        $progressBar = new ProgressBar($output, count($destinations));
        $progressBar->start();

        foreach ($destinations as $destination) {
            fputcsv($csvFile, [
                $destination['name'] ?? '',
                $destination['description'] ?? '',
                $destination['price'] ?? '',
                $destination['duration'] ?? '',
                $destination['image'] ?? '',
            ]);
            $progressBar->advance();
        }

        fclose($csvFile);
        $progressBar->finish();

        $output->writeln("\n<info>CSV export completed successfully. </info>\n");
        $io = new SymfonyStyle($input, $output);
        $io->success('`destinations.csv` created in project root dir.');
        return Command::SUCCESS;
    }
}
