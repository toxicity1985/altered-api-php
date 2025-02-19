<?php

// src/Command/CreateUserCommand.php
namespace Toxicity\AlteredApi\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Toxicity\AlteredApi\Service\AlteredPantherService;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'altered:token:get', description: 'Get Altered token')]
class AlteredTokenCommand extends Command
{
    public function configure(): void
    {
        $this->addArgument('login', InputArgument::REQUIRED, 'login');
        $this->addArgument('password', InputArgument::REQUIRED, 'password');

    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $alteredPantherService = new AlteredPantherService($input->getArgument('login'), $input->getArgument('password'));

        $output->writeln($alteredPantherService->login());

        return Command::SUCCESS;
    }
}