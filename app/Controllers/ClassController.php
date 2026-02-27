<?php

namespace App\Controllers;

use App\Models\Game\Skill;
use App\Utility\DataMapper;
use App\Utility\DataReader;
use Inertia\Inertia;
use Inertia\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;

class ClassController
{
    private static array $classIcons = [
        'Knight' => 'skill-shield-mastery',
        'Mage' => 'skill-wand-mastery',
        'Rogue' => 'skill-dagger-mastery',
        'Summoner' => "skill-summoner's-pact",
        'Warrior' => 'skill-axe-mastery',
        'Acolyte' => 'skill-benediction',
        'Scout' => 'skill-force-shot',
    ];

    #[Get('/classes')]
    public function list(): Redirect
    {
        return new Redirect('https://spiritvale.info/wiki/Classes')->permanent();
    }

    #[Get('/classes/{classId}')]
    public function details(string $classId): Redirect
    {
        return new Redirect('https://spiritvale.info/wiki/' . $classId)->permanent();
    }
}