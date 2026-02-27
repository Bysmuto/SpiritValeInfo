<?php

namespace App\Utility;

use App\Models\Game\Card;
use App\Models\Game\Consumable;
use App\Models\Game\Equipment;
use App\Models\Game\Gem;
use App\Models\Game\Material;
use App\Models\Game\Monster;

class DatabaseUtil
{
    public static function getGameData() : array
    {
        GameDataUtil::loadAllDependencies();
        return [
            'monster' => array_values(Monster::getCached(Monster::class)),
            'equipment' => array_values(Equipment::getCached(Equipment::class)),
            'material' => array_values(Material::getCached(Material::class)),
            'consumable' => array_values(Consumable::getCached(Consumable::class)),
            'card' => array_values(Card::getCached(Card::class)),
            'gem' => array_values(Gem::getCached(Gem::class)),
        ];
    }

    /**
     * @param 'GemDrops'|'Card' $dropType
     */
    public static function getDroppedBy(string $dropType, string $gameId): array
    {
        $monsters = DataReader::readData('game/monsters');
        $droppedBy = [];
        foreach ($monsters as $monster) {
            $drops = $monster[$dropType] ?? [];
            if ($dropType === 'Card') {
                $drops = [$drops];
            }
            foreach ($drops as $drop) {
                if ($drop['Id'] === $gameId) {
                    GameDataUtil::addDependency(Monster::class, $monster['GameId']);;
                    $droppedBy[] = [
                        'monster' => [
                            'id' => $monster['GameId'],
                            'slug' => $monster['Slug'],
                            'name' => $monster['DisplayName'],
                            'isBoss' => $monster['IsBoss'],
                            'level' => $monster['Level'],
                        ],
                        'chance' => $drop['DropChance']
                    ];
                }
            }
        }

        return $droppedBy;
    }
}