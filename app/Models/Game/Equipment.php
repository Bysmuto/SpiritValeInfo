<?php

namespace App\Models\Game;

use App\Utility\DataConverter;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use App\Utility\GameDataUtil;
use App\Utility\UrlUtil;
use Dflydev\DotAccessData\Util;

class Equipment extends AbstractGameModel
{
    public string $DisplayName;
    public string $Description;
    public string $Type;
    public array $PrimaryStats;
    public array $SecondaryStats;
    public int $Slots;
    public ?string $Set;
    public int $Element;

    //internal
    public string $icon;
    public string $typeName;
    public array $statsPrimary;
    public array $statsSecondary;



    public array $drops = [];
    public null|array $crafting = null;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->icon = $this->Slug;
        $this->typeName = DataConverter::getEquipmentTypeName($this->Type);

        $statsPrimary = [];
        foreach ($this->PrimaryStats as $stat) {
            $statString = DataConverter::statToString($stat);
            if ($statString !== '') {
                $statsPrimary[] = $statString;
            }
        }

        $statsSecondary = [];
        foreach ($this->SecondaryStats as $stat) {
            $statsSecondary[] = DataConverter::statToString($stat);
        }

        $this->statsPrimary = $statsPrimary;
        $this->statsSecondary = $statsSecondary;
    }

    protected static function mapDataMultiple(array $list, bool $hydrate = true): array
    {
        return self::createObjectList(Equipment::class, $list);
    }

    public static function loadAdditionalData(array $list): void
    {
        $monsters = DataReader::readDataByClassName(Monster::class);

        $dropMap = [];
        foreach ($monsters as $monster) {
            foreach ($monster['EquipDrops'] as $drop) {
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
                GameDataUtil::addDependency(Monster::class, $monster['Slug']);
            }
        }

        $craftingList = DataReader::readData('game/crafting');

        foreach ($list as $equipment) {
            $drops = $dropMap[$equipment->GameId] ?? [];
            usort($drops, function ($a, $b) {
                return $a['chance'] === $b['chance'] ? strcmp($a['monster']['name'], $b['monster']['name']) : ($a['chance'] < $b['chance'] ? 1 : -1);
            });

            $crafting = null;
            if (isset($craftingList[$equipment->GameId])) {
                $craftingInfo = $craftingList[$equipment->GameId];
                $materials = [];
                foreach ($craftingInfo['materials'] as $gameId => $count) {
                    $item = Material::findOneBy('GameId', $gameId);
                    if ($item === null) {
                        continue;
                    }
                    $materials[] = [
                        'item' => $item,
                        'count' => $count,
                    ];
                }

                $crafting = [
                    'map' => Map::findOneBy('GameId', $craftingInfo['map']),
                    'materials' => $materials,
                ];
            }

            $equipment->drops = $drops;
            $equipment->crafting = $crafting;
        }
    }
}