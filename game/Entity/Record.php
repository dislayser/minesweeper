<?php

declare(strict_types=1);

namespace Game\Entity;

class Record extends Entity
{
    public function __construct() {
        parent::__construct("records");
    }
}