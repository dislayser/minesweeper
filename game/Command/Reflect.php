<?php

declare(strict_types=1);

namespace Game\Command;

use Game\MineSweeper\Game;
use Reflection;
use ReflectionClass;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'game:ref')]
class Reflect extends Command
{
    // Описание команды
    protected function configure()
    {
        $this
            ->setDescription('Тестирование создания таблиц')
            ->setHelp('Эта команда показывает пример использования Symfony Console.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        function resolve(object|string $className): void
        {
            $ref = new ReflectionClass($className);
            if (!$ref->getConstructor()) return;
            
            $consParams = $ref->getConstructor()->getParameters();
            foreach ($consParams as $param) {
                dump($param);
                resolve((string)$param->getType());
            }
        }

        resolve(Game::class);

        return Command::SUCCESS;
    }
}