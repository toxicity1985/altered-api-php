<?php

namespace Toxicity\AlteredApi\Command;

use Toxicity\AlteredApi\Lib\Sets;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Toxicity\AlteredApi\Service\SetService;

#[AsCommand(
    name: 'altered:set:get',
    description: 'Get set.',
)]
class AlteredSetCommand extends Command
{
    public function __construct(private readonly SetService $setService) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach (Sets::all() as $data) {
            $set = $this->setService->buildFromData($data);
            $this->setService->save($set);
        }

        return Command::SUCCESS;
    }
}
