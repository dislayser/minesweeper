<?php

namespace Game\Command;

use Game\MineSweeper\CellType\Bomb;
use Game\MineSweeper\CellType\Number;
use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Live;
use Game\MineSweeper\Player;
use Game\MineSweeper\Server;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'game:start')]
class Start extends Command
{
    // Описание команды
    protected function configure()
    {
        $this
            ->setDescription('Старт игры')
            ->setHelp('Эта команда показывает пример использования Symfony Console.')
            ->addOption(
                'rows', // Имя опции
                'x',    // Алиас (короткая форма)
                InputOption::VALUE_REQUIRED, // Тип значения
                'Количество строк', // Описание
                20      // Значение по умолчанию
            )
            ->addOption(
                'cols',
                'y',
                InputOption::VALUE_REQUIRED,
                'Количество столбцов',
                40
            )
            ->addOption(
                'seed',
                's',
                InputOption::VALUE_OPTIONAL,
                'Сид для генерации',
                null
            )
            ->addOption(
                'bombs',
                'b',
                InputOption::VALUE_REQUIRED,
                'Колличество бомб',
                32
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cols = (int) $input->getOption('cols');
        $rows = (int) $input->getOption('rows');
        $bombs= (int) $input->getOption('bombs');
        $seed = $input->getOption('seed');
        
        dump([$cols, $rows, $bombs, $seed]);
        
        /**
         * @var \Game\MineSweeper\Interfaces\ServerInterface
         */
        $server = new Server();

        /**
         * @var \Game\MineSweeper\Interfaces\GameInterface $game
         * @var \Game\MineSweeper\Interfaces\FieldInterface $filed
         */
        $game = new Game(Game::TYPE_SP, $filed = new Field(
            $cols,
            $rows,
            $bombs,
            $seed
        ));

        $game->addPlayer(
            new Player(
                "session_id",
                new Live(1)
            )
        );

        $server->addGame($game);

        // dump($server);
        for ($r = 0; $r < $filed->getRows(); $r++) {
            for ($c = 0; $c < $filed->getCols(); $c++) {
                $cell = $filed->getCell($c, $r);
                if ($cell->isBomb()) {
                    echo " x ";  
                } else {
                    $n = $cell->getNumber() === 10 ? " " : $cell->getNumber();
                    echo " {$n} ";
                }
            }
            echo PHP_EOL;
        }

        return Command::SUCCESS;
    }
}   