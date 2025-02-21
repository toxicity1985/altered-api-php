<?php

namespace Toxicity\AlteredApi\Command;

use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Toxicity\AlteredApi\Lib\User;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'altered:user:get', description: 'Get Altered token')]
class AlteredUserCommand extends Command
{
    public function configure(): void
    {
        $this->addArgument('token', InputArgument::REQUIRED, 'token');
    }

    /**
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $me = User::me($input->getArgument('token'));

        $output->writeln(json_encode($me));

        return Command::SUCCESS;
    }
}
