<?php

namespace App\Models\Game;

use App\Utility\DataReader;

class Map extends AbstractGameModel
{
    public string $DisplayName;
    public int $MonsterMinLevel;
    public int $MonsterMaxLevel;
    public array $MonsterPool;
    public ?string $BossMonster;

    //internal
    /** @var Monster[] */
    public array $monsters;


    public static function loadAdditionalData(array $list): void
    {
        $monsters = DataReader::readDataByClassName(Monster::class);;

        foreach ($list as $map) {
            $mapMonstersRaw = array_filter($monsters, function ($monster) use ($map) {
                return in_array($monster['GameId'], $map->MonsterPool) || $monster['GameId'] === $map->BossMonster;
            });

            $mapMonsters = Monster::mapDataMultiple($mapMonstersRaw);
            $map->monsters = $mapMonsters;
        }
    }
}