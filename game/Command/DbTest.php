<?php

declare(strict_types=1);

namespace Game\Command;

use Game\Database\MySQL\Column;
use Game\Database\MySQL\ColumnType;
use Game\Database\MySQL\ColumnMod;
use Game\Database\MySQL\CreateTable;
use Game\Database\QueryBuilder;
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
            ->setDescription('Тестирование создания таблиц')
            ->setHelp('Эта команда показывает пример использования Symfony Console.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $createUsers = new CreateTable("users", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("name",      new ColumnType\VarcharType(255), new ColumnMod\NullValue()),
            new Column("login",     new ColumnType\VarcharType(31), new ColumnMod\UniqValue()),
            new Column("password",  new ColumnType\VarcharType(255)),
            new Column("comment",   new ColumnType\TextType(), new ColumnMod\NullValue()),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $createFiles = new CreateTable("files", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("user_id",   new ColumnType\IntegerType(), new ColumnMod\NullValue()),
            new Column("title",     new ColumnType\VarcharType(255), new ColumnMod\NullValue()),
            new Column("name",      new ColumnType\VarcharType(255), new ColumnMod\UniqValue()),
            new Column("type",      new ColumnType\VarcharType(31)),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $createServers = new CreateTable("servers", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("title",     new ColumnType\VarcharType(255), new ColumnMod\NullValue()),
            new Column("name",      new ColumnType\VarcharType(255), new ColumnMod\UniqValue()),
            new Column("game_id",   new ColumnType\IntegerType()),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $createGames = new CreateTable("games", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("difficult_id",  new ColumnType\IntegerType()),
            new Column("rows",      new ColumnType\IntegerType()),
            new Column("cols",      new ColumnType\IntegerType()),
            new Column("seed",      new ColumnType\IntegerType()),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $createDifficult = new CreateTable("difficult", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("name",      new ColumnType\VarcharType(255)),
            new Column("bomb_ratio",new ColumnType\RealType()),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $createRecords = new CreateTable("records", [
            new Column("id",        new ColumnType\IntegerType(), new ColumnMod\Primary()),
            new Column("game_id",   new ColumnType\IntegerType()),
            new Column("user_id",   new ColumnType\IntegerType()),
            new Column("game_start",new ColumnType\DateTimeType()),
            new Column("game_end",  new ColumnType\DateTimeType()),
            new Column("created",   new ColumnType\DateTimeType(), new ColumnMod\DefaultValue("CURRENT_TIMESTAMP")),
            new Column("deleted",   new ColumnType\BoolType(), new ColumnMod\DefaultValue(0)),
        ]);

        $qb = (new QueryBuilder())
            ->select(["table_name"])
            ->from("information_schema.tables")
            ->andWhere("table_schema = :table")
            ->addParam("table", "app")
        ;
        
        dd((string) $qb, $qb->getParams());
        
        dump($createUsers, $createFiles);
        
        return Command::SUCCESS;
    }
}