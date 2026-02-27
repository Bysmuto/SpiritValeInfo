<?php

namespace App\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Tempest\Http\Responses\Json;
use Tempest\Router\Get;
use function Tempest\env;

class ServerStatusController
{
    #[Get('/game/server-status')]
    public function page(): Response
    {
        return inertia('ServerStatusPage', [
            'servers' => Inertia::defer(fn() => self::getServerListData()),
        ]);
    }

    private function getServerListData(): array
    {
        $serverListApi = env('SERVER_LIST_API', '');
        if (!is_string($serverListApi) || $serverListApi === '') {
            return [];
        }

        $serverListJson = file_get_contents($serverListApi);
        $serverList = json_decode($serverListJson, true);
        if ($serverList === null) {
            return [];
        }

        return array_map(function ($server) {
            return ['name' => $server['name'], 'region' => $server['region'], 'players' => $server['players']];
        }, $serverList);
    }
}
