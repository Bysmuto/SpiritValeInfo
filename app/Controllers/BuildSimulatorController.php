<?php

namespace App\Controllers;

use App\Models\Game\GameClass;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;

class BuildSimulatorController
{
    #[Get('/build-simulator')]
    public function buildSimulator(): Response
    {
        $classes = DataReader::readData('game/classes');
        $skillList = DataReader::readData('game/skills');

        $publicSkillTree = [];
        $skills = [];
        $skillMap = [];

        foreach ($classes as $class) {
            $skillIdListTemp = [];
            $publicSkillTree[$class['DisplayName']] = [[], [], [], [], [], []];
            foreach ($class['CustomSkillTree'] as $skillRow) {
                foreach ($skillRow as $skillGameId) {
                    if ($skillGameId !== '') {
                        $skillIdListTemp[] = $skillGameId;
                    }
                }
            }

            foreach ($skillIdListTemp as $id) {
                if (isset($skillList[$id])) {
                    $skill = $skillList[$id];

                    $requirements = [];
                    foreach ($skill['Requirements'] as $requirement) {
                        if (isset($skillList[$requirement['Id']])) {
                            $reqSkill = $skillList[$requirement['Id']];
                            $requirements[] = [
                                'id' => strtolower(str_replace(' ', '-', $reqSkill['DisplayName'])),
                                'name' => $reqSkill['DisplayName'],
                                'level' => $requirement['Level'],
                            ];
                        }
                    }

                    $skills[$id] = [
                        'id' => strtolower(str_replace(' ', '-', $skill['DisplayName'])),
                        'name' => $skill['DisplayName'],
                        'description' => $skill['Description'],
                        'maxLevel' => $skill['MaxLv'],
                        'isPassive' => $skill['CastType'] === null,
                        'requirements' => $requirements,
                        'values' => DataMapper::skillValues($skill),
                    ];

                    $skillMap[strtolower(str_replace(' ', '-', $skill['DisplayName']))] = [
                        'id' => strtolower(str_replace(' ', '-', $skill['DisplayName'])),
                        'name' => $skill['DisplayName'],
                        'description' => $skill['Description'],
                        'maxLevel' => $skill['MaxLv'],
                        'isPassive' => $skill['CastType'] === null,
                        'requirements' => $requirements,
                        'values' => DataMapper::skillValues($skill),
                    ];
                }
            }

            for ($row = 0; $row < 6; $row++) {
                for ($col = 0; $col < 7; $col++) {
                    $skillId = $class['CustomSkillTree'][$row][$col] ?? '';
                    $skill = $skills[$skillId] ?? null;

                    $publicSkillTree[$class['DisplayName']][$row][$col] = $skill === null ? null : $skill;
                }
            }
        }

        $frontEndData = [
            'classes' => GameClass::getAll(),
            'classSkillTrees' => $publicSkillTree,
            'skills' => array_values($skills),
            'skillMap' => $skillMap,
        ];

        return inertia('BuildSimulatorPage', $frontEndData);
    }
}
