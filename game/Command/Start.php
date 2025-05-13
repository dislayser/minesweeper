<?php

namespace Game\Command;

use Game\MineSweeper\CellType\Bomb;
use Game\MineSweeper\CellType\Number;
use Game\MineSweeper\Difficult;
use Game\MineSweeper\Field;
use Game\MineSweeper\Game;
use Game\MineSweeper\Player;
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
                10      // Значение по умолчанию
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
                InputOption::VALUE_REQUIRED,
                'Сид для генерации',
                null
            )
            ->addOption(
                'difficult',
                'd',
                InputOption::VALUE_REQUIRED,
                'Сложность [easy, medium, hard]',
                "easy"
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $x = (int) $input->getOption('rows');
        $y = (int) $input->getOption('cols');
        $seed = (int) $input->getOption('seed');
        $difficultName = (string) $input->getOption('difficult');
        dump([$x,$y,$seed, $difficultName]);
        $game = new Game(
            new Player(),
            new Field($x,$y, $seed)
        );

        if (in_array($difficultName, ["easy", "medium", "hard"])){
            if (method_exists(Difficult::class, $difficultName)){
                $difficult = Difficult::$difficultName();
            }else{
                $difficult = Difficult::easy();
            }
        }
        $game->setDifficult($difficult);
        $game->buildField();

        // Вывод
        // dd($game->field()->getX());
        for ($y = 0; $y < $game->field()->getY(); $y++) {
            for ($x = 0; $x < $game->field()->getX(); $x++) {
                /** @var Bomb|Number $cell */
                $cell = $game->field()->getCell($x,$y);
                // dump($cell);
                echo $cell->isBomb() ? " x " : " " . ($cell->getBombNear() === 0 ? " " : $cell->getBombNear()) . " ";
            }
            echo PHP_EOL;
        }

        return Command::SUCCESS;
    }
}   