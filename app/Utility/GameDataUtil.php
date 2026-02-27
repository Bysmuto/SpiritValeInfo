<?php

namespace App\Utility;

class GameDataUtil
{
    private static array $missingDependencies = [];

    public static function addDependency(string $className, string $id): void
    {
        if (!isset(self::$missingDependencies[$className])) {
            self::$missingDependencies[$className] = [];
        }
        self::$missingDependencies[$className][] = $id;
    }

    public static function loadAllDependencies(): void
    {
        foreach (self::$missingDependencies as $className => $idList) {
            $className::getByIds(array_unique($idList));
        }
    }
}