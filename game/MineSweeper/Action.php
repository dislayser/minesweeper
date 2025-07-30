<?php

declare(strict_types=1);

namespace Game\MineSweeper;

class Action implements Interfaces\ActionInterface
{
    public const TYPE_OPENCELL = "OPENCELL";
    public const TYPE_OPENCELLS = "OPENCELLS";
    public const TYPE_SETFLAG = "SETFLAG";
    public const TYPE_CHECKLIVE = "CHECKLIVE";

    public function __construct(
        private string $type,
        private int|string $playerId,
        private ?array $options = null,
    ) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getPlayerId(): int|string
    {
        return $this->playerId;
    }

    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @return array{col: int, row: int}
     */
    public function getCellData(): array
    {
        return [
            "col" => $this->options["col"] ?? -1,
            "row" => $this->options["row"] ?? -1,
        ];
    }

    /**
     * @return array<array{0: int, 1: int}>
     */
    public function getCellsData(): array
    {
        $data = [];
        if (is_array($this->options)) foreach ($this->options as $cell) {
            $cell = array_values($cell);
            if (is_array($cell) && count($cell) === 2) {
                $data[] = [(int) $cell[0], (int) $cell[1]];
            }
        }
        return $data;
    }
}