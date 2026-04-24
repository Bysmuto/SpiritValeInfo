<?php

namespace App\Controllers;

use Tempest\Http\Response;
use Tempest\Http\Responses\NotFound;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;

class WikiController
{
    #[Get('/wiki/{slug}')]
    public function generalWikiPage(string $slug): Response
    {
        $redirects = [
            'gold' => 'Coin',
            'npc' => 'Non-Player_Character',
        ];

        $slugLower = strtolower($slug);
        if (isset($redirects[$slugLower])) {
            return new Redirect('/wiki/' . $redirects[$slugLower]);
        }

        $fileName = $slug . 'Page';
        $filePath = __DIR__ . "/../pages/wiki/$fileName.vue";
        if (!file_exists($filePath)) {
            error_log("WikiController::generalWikiPage(): File not found: $filePath");
            return new NotFound();
        }
        
        return inertia("wiki/$fileName");
    }
}