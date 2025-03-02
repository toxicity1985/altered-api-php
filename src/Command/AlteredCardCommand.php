<?php

namespace Toxicity\AlteredApi\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Toxicity\AlteredApi\Lib\Cards;

#[AsCommand(
    name: 'altered:card:get',
    description: 'Get card.',
)]
class AlteredCardCommand extends Command
{

    public function configure(): void
    {
        $this->addArgument('reference', InputArgument::REQUIRED, 'reference');
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln(json_encode(Cards::byReference($input->getArgument('reference'))));
        $output->writeln(json_encode(Cards::alternateCardsByReference($input->getArgument('reference'))));

        return Command::SUCCESS;
    }
}
