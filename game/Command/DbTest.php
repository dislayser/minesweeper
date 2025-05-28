<?php

declare(strict_types=1);

namespace Game\Command;

use Game\Database\MySQL\Column;
use Game\Database\MySQL\ColumnType;
use Game\Database\MySQL\CreateTable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


#[AsCommand(name: 'game:db_test')]
class DbTest extends Command
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
        $create = new CreateTable("users", [
            new Column("id", [
                new ColumnType\Primary(),
                new ColumnType\IntegerType(),
            ]),
            new Column("name", [
                new ColumnType\VarcharType(255),
                new ColumnType\NullType(),
            ]),
            new Column("login", [
                new ColumnType\VarcharType(31),
                new ColumnType\UniqType(),
            ]),
            new Column("password", [
                new ColumnType\VarcharType(255),
            ]),
            new Column("comment", [
                new ColumnType\TextType(),
                new ColumnType\NullType(),
            ]),
            new Column("created", [
                new ColumnType\Def("CURRENT_TIMESTAMP"),
                new ColumnType\DateTimeType(),
                new ColumnType\NullType(),
            ]),
            new Column("deleted", [
                new ColumnType\BoolType(),
                new ColumnType\Def(0),
            ]),
        ]);

        dump($create);
        return Command::SUCCESS;
    }
}