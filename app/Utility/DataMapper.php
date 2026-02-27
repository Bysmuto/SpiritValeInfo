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
use Tempest\Router\Get;

class DataMapper
{
    public static array $classIcons = [
        'Knight' => 'skill-shield-mastery',
        'Mage' => 'skill-wand-mastery',
        'Rogue' => 'skill-dagger-mastery',
        'Summoner' => "skill-summoner's-pact",
        'Warrior' => 'skill-axe-mastery',
        'Acolyte' => 'skill-benediction',
        'Scout' => 'skill-force-shot',
        'Weaver' => 'skill-weaver-mastery'
    ];



    public static function skillValues(array $skill): array
    {
        $values = [];
        if (isset($skill['Damage']) && ($skill['Damage']['Value'] > 0 || $skill['Damage']['ValueLv'] > 0)) {
            $values['damage'] = [
                'base' => $skill['Damage']['Value'] * 100,
                'level' => $skill['Damage']['ValueLv'] * 100,
                'scaling' => self::skillDamageScaling($skill),
            ];
        }

        if (isset($skill['Cooldown']) && (($skill['Cooldown']['Value'] > 0) || $skill['Cooldown']['ValueLv'] > 0)) {
            $values['cooldown'] = [
                'base' => $skill['Cooldown']['Value'],
                'level' => $skill['Cooldown']['ValueLv'],
            ];
        }
        if (isset($skill['Cost']) && (($skill['Cost']['Value'] > 0) || $skill['Cost']['ValueLv'] > 0)) {
            $values['cost'] = [
                'base' => $skill['Cost']['Value'],
                'level' => $skill['Cost']['ValueLv'],
            ];
        }
        return $values;
    }


    private static function skillDamageScaling(array $skill): array
    {
        $scalings = [];

        foreach ($skill['Scalings'] as $scaling) {
            $type = explode('_', $scaling['Name'])[0];
            if ($type === 'Str') {
                $scalings[] = ['type' => 'STR', 'value' => $scaling['Value']];
            }
            if ($type === 'Agi') {
                $scalings[] = ['type' => 'AGI', 'value' => $scaling['Value']];
            }
            if ($type === 'Dex') {
                $scalings[] = ['type' => 'DEX', 'value' => $scaling['Value']];
            }
            if ($type === 'Vit') {
                $scalings[] = ['type' => 'VIT', 'value' => $scaling['Value']];
            }
            if ($type === 'Int') {
                $scalings[] = ['type' => 'INT', 'value' => $scaling['Value']];
            }
            if ($type === 'Luk') {
                $scalings[] = ['type' => 'LUK', 'value' => $scaling['Value']];
            }
        }

        return $scalings;
    }
}