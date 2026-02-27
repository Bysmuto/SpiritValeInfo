<?php

namespace App\Models\Game;

use App\Utility\DataMapper;
use App\Utility\DataReader;

class GameClass extends AbstractGameModel
{
    public string $DisplayName;
    public string $Description;
    public string $Type;
    public int $MaxJobLevel;
    public array $SkillTree;
    public array $AdvancedClasses;

    //internal
    public ?string $icon;


    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->icon = DataMapper::$classIcons[$this->DisplayName] ?? null;
    }
}