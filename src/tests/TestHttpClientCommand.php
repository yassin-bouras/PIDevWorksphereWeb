<?php

namespace App\tests;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand(name: 'app:test-http-client')]
class TestHttpClientCommand extends Command
{
    public function __construct(private HttpClientInterface $client)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $response = $this->client->request('GET', 'https://www.google.com');
            $output->writeln('Status: ' . $response->getStatusCode());
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Request failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
