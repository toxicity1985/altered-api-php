<?php

namespace Toxicity\AlteredApi\Command;

use Toxicity\AlteredApi\Lib\Sets;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'altered:set:get',
    description: 'Get set.',
)]
class AlteredSetCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (Sets::all() as $data) {
            $output->writeln(json_encode($data));
        }

        return Command::SUCCESS;
    }
}
