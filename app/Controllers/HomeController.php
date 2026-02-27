<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Utility\DataReader;
use Tempest\Http\Response;
use Tempest\Http\Responses\Redirect;
use Tempest\Router\Get;

final readonly class HomeController
{
    #[Get('/')]
    public function __invoke(): Response
    {
        $wikiPages = DataReader::readData('wiki');
        return inertia('HomePage', ['wikiPages' => $wikiPages]);
    }

    #[Get('/world-map')]
    public function worldPage(): Response
    {
        return inertia('WorldMapPage');
    }

    #[Get('/faq')]
    public function faqPage(): Response
    {
        return inertia('FaqPage', ['categories' => DataReader::readData('faq')]);
    }

    #[Get('/guides')]
    public function guidesPage(): Response
    {
        return inertia('GuidesPage');
    }

    #[Get('/guide/{id}/{slug}')]
    public function guidePage(int $id, string $slug): Response
    {
        $guides = DataReader::readData('guides');
        $guide = array_find($guides, fn($g) => $g['id'] === $id);
        if (!$guide) {
            return new Redirect('/guides');
        }
        $filePath = __DIR__ . '/../../data/guides/' . $id . '/content.md';
        if (!file_exists($filePath)) {
            $filePath = __DIR__ . '/../../example-data/guides/' . $id . '/content.md';
        }
        $content = file_get_contents($filePath);
        return inertia('GuidePage', ['guide' => [...$guide, 'content' => $content]]);
    }

    #[Get('/team')]
    public function teamPage(): Response
    {
        return inertia('TeamPage');
    }
}
