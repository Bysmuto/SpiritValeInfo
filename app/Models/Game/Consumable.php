<?php

namespace App\Models\Game;

use App\Utility\DataConverter;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use App\Utility\UrlUtil;

class Consumable extends AbstractGameModel
{

    public string $DisplayName;
    public string $Description;
    public int $Reusable;
    public int $Type;
    public array $Value;

    //internal
    public string $icon;

    //additional
    public array $drops = [];

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->icon = $this->Slug;
    }

    public static function getByMonsterGameId(string $monsterGameId): ?Consumable
    {
        $consumables = self::readGameData();
        foreach ($consumables as $consumable) {
            if ($consumable['Type'] === 1 && isset($consumable['Value']['ValueStr']) && $consumable['Value']['ValueStr'] === $monsterGameId) {
                return self::mapDataMultiple([$consumable])[0];
            }
        }

        return null;
    }

    public static function loadAdditionalData(array $list): void
    {
        $monsters = DataReader::readDataByClassName(Monster::class);

        $dropMap = [];
        foreach ($monsters as $monster) {
            foreach ($monster['ConsumableDrops'] as $drop) {
                if (!isset($dropMap[$drop['Id']])) {
                    $dropMap[$drop['Id']] = [];
                }
                $dropMap[$drop['Id']][] = [
                    'monster' => [
                        'name' => $monster['DisplayName'],
                        'isBoss' => $monster['IsBoss'],
                        'level' => $monster['Level'],
                        'slug' => $monster['Slug'],
                    ],
                    'chance' => $drop['DropChance']
                ];
            }
        }

        foreach ($list as $consumable) {
            $drops = $dropMap[$consumable->GameId] ?? [];
            usort($drops, function ($a, $b) {
                return $a['chance'] === $b['chance'] ? strcmp($a['monster']['name'], $b['monster']['name']) : ($a['chance'] < $b['chance'] ? 1 : -1);
            });
            $consumable->drops = $drops;
        }
    }
}