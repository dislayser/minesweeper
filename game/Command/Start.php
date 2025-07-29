<?php

declare(strict_types=1);

namespace Game\Command;

use Game\MineSweeper\Action;
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
            ->setHelp('Эта комманда тестирует игру.')
            ->addOption(
                'rows',     // Имя опции
                'x',    // Алиас (короткая форма)
                InputOption::VALUE_REQUIRED, // Тип значения
                'Количество строк', // Описание
                10       // Значение по умолчанию
            )
            ->addOption(
                'cols',
                'y',
                InputOption::VALUE_REQUIRED,
                'Количество столбцов',
                10
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
                20
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cols = (int) $input->getOption('cols' );
        $rows = (int) $input->getOption('rows' );
        $bombs= (int) $input->getOption('bombs');
        $seed =       $input->getOption('seed' );
        
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

        $game->addPlayer($player = new Player(
            $playerId = "session_id",
            new Live(1)
        ));

        $server->addGame($game);

        dump($server);
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

        dump($filed->getCell(0,0));
        dump($server->doAction(
            new Action(Action::TYPE_OPENCELLS, $playerId, [
                [0,0],
                [1,0],
                [2,0],
                [0,1],
                [1,1],
            ])
        ));
        dump($player);

        return Command::SUCCESS;
    }
}   