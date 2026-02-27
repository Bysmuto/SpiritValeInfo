<?php

namespace App\Models\Game;

use App\Utility\DataConverter;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use App\Utility\UrlUtil;

class Gem extends AbstractGameModel
{
    public string $DisplayName;
    public string $Description;
    public string $Affix;
    public int $IsBoss;
    public array $Stats;

    //internal
    public string $icon;
    public array $statsTexts;

    //additional
    public array $drops = [];

    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->icon = 'item-' . ($data['IsBoss'] ? 'gem-boss' : 'gem-skill');
        $stats = [];
        foreach ($this->Stats as $stat) {
            $stats[] = DataConverter::statToString($stat);
        }
        $this->statsTexts= $stats;
    }


    public static function loadAdditionalData(array $list): void
    {
        $monsters = DataReader::readDataByClassName(Monster::class);

        $dropMap = [];
        foreach ($monsters as $monster) {
            foreach ($monster['GemDrops'] as $drop) {
                if (!isset($dropMap[$drop['Id']])) {
                    $dropMap[$drop['Id']] = [];
                }

            }
        }

        foreach ($list as $gem) {
            $drops = $dropMap[$gem->gameId] ?? [];
            usort($drops, function ($a, $b) { return $a['chance'] === $b['chance'] ? strcmp($a['monster']['name'], $b['monster']['name']) : ($a['chance'] < $b['chance'] ? 1 : -1); });
            $gem->drops = $drops;
        }
    }
}