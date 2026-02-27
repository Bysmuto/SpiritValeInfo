<?php

namespace App\Models\Game;

use App\Utility\DataConverter;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use App\Utility\UrlUtil;

class Card extends AbstractGameModel
{
    public string $DisplayName;

    public string $Affix;
    public string $Description;
    public int $IsBoss;

    //internal
    public string $icon;
    public string $slot;
    public array $stats = [];

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->icon = 'item-' . ($data['IsBoss'] ? 'card-boss' : 'card-normal');
        $this->slot = DataConverter::getSlotName($data['EquipClass']);

        $stats = [];
        foreach ($data['Stats'] as $stat) {
            $stats[] = DataConverter::statToString($stat);
        }
        $this->stats = $stats;
    }
}