<?php

namespace App\Utility;

use App\Models\Game\Card;
use App\Models\Game\Consumable;
use App\Models\Game\Equipment;
use App\Models\Game\GameClass;
use App\Models\Game\Gem;
use App\Models\Game\Map;
use App\Models\Game\Material;
use App\Models\Game\Monster;
use App\Models\Game\Skill;
use function Tempest\map;

class DataReader
{
    private static array $cache = [];

    public static function readData(string $name): array
    {
        $baseDirectoryPath = self::getBaseDirectoryPath();
        $filePath = $baseDirectoryPath . '/' . $name . '.json';
        if (!file_exists($filePath)) {
            return [];
        }

        if (!isset(self::$cache[$name])) {
            self::$cache[$name] = map(file_get_contents($filePath))->toArray();
        }
        return self::$cache[$name];
    }

    public static function readDataByClassName(string $className): array
    {
        $filePath = match ($className) {
            Card::class => 'game/cards',
            Consumable::class => 'game/consumables',
            Equipment::class => 'game/equipment',
            GameClass::class => 'game/classes',
            Gem::class => 'game/gems',
            Map::class => 'game/maps',
            Material::class => 'game/materials',
            Monster::class => 'game/monsters',
            Skill::class => 'game/skills',
            default => null,
        };
        if ($filePath === null) {
            error_log('invalid class name: ' . $className);
            return [];
        }
        return self::readData($filePath);
    }

    private static function getBaseDirectoryPath(): string
    {
        $dataDirs = ['data', 'example-data'];
        foreach ($dataDirs as $dataDir) {
            $dataDirPath = __DIR__ . '/../../' . $dataDir;
            if (is_dir($dataDirPath)) {
                return $dataDirPath;
            }
        }
        throw new \RuntimeException('Missing data directory');
    }
}