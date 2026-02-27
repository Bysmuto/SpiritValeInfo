<?php

namespace App\Models\Game;

use App\Utility\DataMapper;
use App\Utility\DataReader;
use App\Utility\UrlUtil;

class Skill extends AbstractGameModel
{
    public string $DisplayName;
    public string $Description;
    public ?int $CastType;
    public int $MaxLv;

    //internal
    public bool $isPassive;
    /** @var array{ name: string, level: int }[] */
    public array $requirements;
    public array $values;

    public function __construct(array $data, array $requirements)
    {
        parent::__construct($data);


        $this->isPassive = $this->CastType === null;
        $this->requirements = $requirements;

        $this->values = DataMapper::skillValues($data);
    }

    protected static function mapDataMultiple(array $list): array
    {
        $skills = [];
        foreach($list as $rawSkill) {
            $requirements = [];
            foreach ($rawSkill['Requirements'] as $requirement) {
                if (isset($skillList[$requirement['Id']])) {
                    $reqSkill = $skillList[$requirement['Id']];
                    $requirements[] = ['name' => $reqSkill['DisplayName'], 'level' => $requirement['Level']];
                }
            }

            $skill = new Skill($rawSkill, $requirements);
            $skills[$rawSkill['GameId']] = $skill;
        }

        return $skills;
    }
}