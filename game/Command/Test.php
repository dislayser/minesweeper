<?php

declare(strict_types=1);

namespace Game\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'game:test')]
class Test extends Command
{
    // Описание команды
    protected function configure()
    {
        $this
            ->setDescription('Старт игры')
            ->setHelp('Эта команда показывает пример использования Symfony Console.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $number = 123;
        $array = str_split((string) $number);
        dump(max($array));
        return Command::SUCCESS;
    }
}