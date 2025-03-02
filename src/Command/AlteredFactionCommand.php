<?php

namespace Toxicity\AlteredApi\Command;

use Toxicity\AlteredApi\Lib\Factions;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'altered:faction:get',
    description: 'Get faction.',
)]
class AlteredFactionCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (Factions::all() as $data) {
            $output->writeln(json_encode($data));
        }

        return Command::SUCCESS;
    }
}
