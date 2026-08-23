<?php

namespace Database\Seeders;

use App\Models\SocialLink;
use Illuminate\Database\Seeder;

class SocialLinkSeeder extends Seeder
{
    /**
     * Seeds social_links from the data currently hardcoded in
     * config/portfolio.php ('social_links'), preserving array order
     * as sort_order.
     */
    public function run(): void
    {
        $links = config('portfolio.social_links');

        foreach ($links as $index => $link) {
            SocialLink::updateOrCreate(
                ['platform' => $link['platform']],
                [
                    'name' => $link['name'],
                    'url' => $link['url'],
                    'username' => $link['username'] ?? null,
                    'icon' => $link['icon'],
                    'bg_color' => $link['bg_color'] ?? null,
                    'text_color' => $link['text_color'] ?? null,
                    'description' => $link['description'] ?? null,
                    'is_active' => true,
                    'sort_order' => $index,
                ]
            );
        }
    }
}
