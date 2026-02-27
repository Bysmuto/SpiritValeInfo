<?php

namespace App\Utility;

use App\Models\Game\Card;
use App\Models\Game\Consumable;
use App\Models\Game\Equipment;
use App\Models\Game\Gem;
use App\Models\Game\Map;
use App\Models\Game\Material;
use function Tempest\env;
use function Tempest\map;

class VendingUtil
{
    public static function getVendingData(?int $filterType, ?string $filterGameId, ?string $filterName): array
    {
        $vendingApi = env('VENDING_API', '');
        if (!is_string($vendingApi) || $vendingApi === '') {
            return [];
        }

        $data = VendingUtil::readUrlCached($vendingApi . '/shops',  30);

        $maps = Map::getAll(false);
        $mapDictionary = [];
        foreach ($maps as $map) {
            $mapDictionary[$map->GameId] = $map;
        }

        $shops = [];
        $materialList = [];
        $consumableList = [];
        $equipmentList = [];
        $cardList = [];
        $gemList = [];

        foreach ($data as $shop) {
            foreach ($shop['selling'] as $entry) {
                $countAvailable = $entry['Count'] - $entry['CountTraded'];

                if ($entry['Type'] === 0 && $countAvailable > 0) {
                    if (!isset($materialList[$entry['Id']])) {
                        $materialList[$entry['Id']] = 0;
                    }
                    $materialList[$entry['Id']] += $countAvailable;
                }
                if ($entry['Type'] === 1 && $countAvailable > 0) {
                    if (!isset($consumableList[$entry['Id']])) {
                        $consumableList[$entry['Id']] = 0;
                    }
                    $consumableList[$entry['Id']] += $countAvailable;
                }
                if ($entry['Type'] === 2 && $countAvailable > 0) {
                    $gameId = $entry['Json']['Id'];
                    if (!isset($equipmentList[$gameId])) {
                        $equipmentList[$gameId] = 0;
                    }
                    $equipmentList[$gameId] += $countAvailable;
                }
                if ($entry['Type'] === 4 && $countAvailable > 0) {
                    if (!isset($cardList[$entry['Id']])) {
                        $cardList[$entry['Id']] = 0;
                    }
                    $cardList[$entry['Id']] += $countAvailable;
                }
                if ($entry['Type'] === 5 && $countAvailable > 0) {
                    $gameId = $entry['Json']['Id'];
                    if (!isset($gemList[$gameId])) {
                        $gemList[$gameId] = 0;
                    }
                    $gemList[$gameId] += $countAvailable;
                }
            }
        }

        $materials = Material::getAll(false);
        foreach ($materials as $material) {
            if ($filterGameId === null && $filterType === 0 && $filterName === $material->DisplayName) {
                $filterGameId = $material->GameId;
            }
        }

        $consumables = Consumable::getAll(false);
        foreach ($consumables as $consumable) {
            if ($filterGameId === null && $filterType === 1 && $filterName === $consumable->DisplayName) {
                $filterGameId = $consumable->GameId;
            }
        }

        $equipments = Equipment::getAll(false);
        foreach ($equipments as $equipment) {
            if ($filterGameId === null && $filterType === 2 && $filterName === $equipment->DisplayName) {
                $filterGameId = $equipment->GameId;
            }
        }

        $cards = Card::getAll(false);
        foreach ($cards as $card) {
            if ($filterGameId === null && $filterType === 4 && $filterName === $card->DisplayName) {
                $filterGameId = $card->GameId;
            }
        }

        $gems = Gem::getAll(false);
        foreach ($gems as $gem) {
            if ($filterGameId === null && $filterType === 5 && $filterName === $gem->DisplayName) {
                $filterGameId = $gem->GameId;
            }
        }

        foreach ($data as $shop) {
            $showShop = false;
            $itemInfo = null;
            if ($filterType !== null && $filterGameId !== null) {
                $itemInfo = self::hasItem($shop, $filterGameId, $filterType);
                $showShop = $itemInfo !== null;
            } else {
                $showShop = true;
            }

            if ($showShop) {
                $shops[] = [
                    'name' => $shop['name'],
                    'server' => $shop['server'],
                    'map' => isset($mapDictionary[$shop['mapId']]) ? $mapDictionary[$shop['mapId']]->DisplayName : '?',
                    'characterName' => $shop['characterName'],
                    'itemInfo' => $itemInfo,
                ];
            }
        }

        $materialsSold = [];
        foreach ($materials as $material) {
            $materialsSold[] = ['name' => $material->DisplayName, 'count' => $materialList[$material->GameId] ?? 0];
        }
        uasort($materialsSold, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $consumablesSold = [];
        foreach ($consumables as $consumable) {
            $consumablesSold[] = ['name' => $consumable->DisplayName, 'count' => $consumableList[$consumable->GameId] ?? 0];
        }
        uasort($consumablesSold, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $equipmentsSold = [];
        foreach ($equipments as $equipment) {
            $equipmentsSold[] = ['name' => $equipment->DisplayName, 'count' => $equipmentList[$equipment->GameId] ?? 0];
        }
        uasort($equipmentsSold, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $cardsSold = [];
        foreach ($cards as $card) {
            $cardsSold[] = ['name' => $card->DisplayName, 'count' => $cardList[$card->GameId] ?? 0];
        }
        uasort($cardsSold, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $gemsSold = [];
        foreach ($gems as $gem) {
            $gemsSold[] = ['name' => $gem->DisplayName, 'count' => $gemList[$gem->GameId] ?? 0];
        }
        uasort($gemsSold, fn ($a, $b) => strcmp($a['name'], $b['name']));

        $data = [
            'shops' => $shops,
            'materialsSold' => array_values($materialsSold),
            'consumablesSold' => array_values($consumablesSold),
            'equipmentsSold' => array_values($equipmentsSold),
            'cardsSold' => array_values($cardsSold),
            'gemsSold' => array_values($gemsSold),
        ];

        return $data;
    }

    public static function getVendingHistoryData(int $type, string $gameId): array
    {
        $vendingApi = env('VENDING_API', '');
        if (!is_string($vendingApi) || $vendingApi === '') {
            return [];
        }

        $data = VendingUtil::readUrlCached($vendingApi . '/history', 300);

        $history = [];
        foreach ($data as $entry) {
            if ($type === 0 || $type === 1 || $type === 4) {
                if ($entry['Type'] === $type && $entry['Id'] === $gameId) {
                    $history[] = [
                        'count' => $entry['CountTraded'],
                        'price' => $entry['Price'],
                        'sellTime' => $entry['SellTime'],
                        'updateTime' => $entry['UpdateTime'],
                        'refine' => null,
                    ];
                }
            }
            if ($type === 2 || $type === 5) {
                if ($entry['Type'] === $type && $entry['Json']['Id'] === $gameId) {
                    $history[] = [
                        'count' => $entry['CountTraded'],
                        'price' => $entry['Price'],
                        'sellTime' => $entry['SellTime'],
                        'updateTime' => $entry['UpdateTime'],
                        'refine' => $entry['Json']['Refine'],
                    ];
                }
            }
        }

        uasort($history, fn ($a, $b) => strcmp($a['sellTime'], $b['sellTime']));

        return array_values($history);
    }

    public static function readUrlCached(string $url, int $time): array
    {
        $fileName = 'cache_' . md5($url);
        $filePath = __DIR__ . '/../../data/' . $fileName . '.json';

        if (!file_exists($filePath) || filemtime($filePath) < (time() - $time)) {
            $data = file_get_contents($url);
            file_put_contents($filePath, $data);
        }

        return map(file_get_contents($filePath))->toArray();
    }

    private static function hasItem(array $shop, string $filterGameId, int $filterType): null|array
    {
        foreach ($shop['selling'] as $entry) {
            if ($entry['Type'] === $filterType && $entry['Type'] === 0 && $entry['Id'] === $filterGameId && $entry['Count'] > $entry['CountTraded']) { //materials
                return ['count' => $entry['Count'] - $entry['CountTraded'], 'price' => $entry['Price']];
            }
            if ($entry['Type'] === $filterType && $entry['Type'] === 1 && $entry['Id'] === $filterGameId && $entry['Count'] > $entry['CountTraded']) { //consumables
                return ['count' => $entry['Count'] - $entry['CountTraded'], 'price' => $entry['Price']];
            }
            if ($entry['Type'] === $filterType && $entry['Type'] === 2 && $entry['Json']['Id'] === $filterGameId && $entry['Count'] > $entry['CountTraded']) { //equipment
                return ['count' => $entry['Count'] - $entry['CountTraded'], 'price' => $entry['Price'], 'refine' => $entry['Json']['Refine']];
            }
            if ($entry['Type'] === $filterType && $entry['Type'] === 4 && $entry['Id'] === $filterGameId && $entry['Count'] > $entry['CountTraded']) { //cards
                return ['count' => $entry['Count'] - $entry['CountTraded'], 'price' => $entry['Price']];
            }
            if ($entry['Type'] === $filterType && $entry['Type'] === 5 && $entry['Json']['Id'] === $filterGameId && $entry['Count'] > $entry['CountTraded']) { //gems
                return ['count' => $entry['Count'] - $entry['CountTraded'], 'price' => $entry['Price'], 'refine' => $entry['Json']['Refine']];
            }
        }

        return null;
    }
}