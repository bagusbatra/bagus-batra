<?php

namespace Database\Seeders;

use App\Models\SiteProfile;
use Illuminate\Database\Seeder;

class SiteProfileSeeder extends Seeder
{
    /**
     * Seeds the singleton site_profiles row (id = 1) from the data
     * currently hardcoded in config/portfolio.php ('personal_info').
     */
    public function run(): void
    {
        $info = config('portfolio.personal_info');

        SiteProfile::updateOrCreate(
            ['id' => 1],
            [
                'name' => $info['name'],
                'nickname' => $info['nickname'],
                'title_id' => $info['title_id'],
                'title_en' => $info['title_en'],
                'tagline_id' => $info['tagline_id'],
                'tagline_en' => $info['tagline_en'],
                'bio_id' => $info['bio_id'],
                'bio_en' => $info['bio_en'],
                'location' => $info['location'],
                'email' => $info['email'],
                'phone' => $info['phone'],
                'github' => $info['github'],
                'linkedin' => $info['linkedin'],
                'twitter' => $info['twitter'],
                'available_for_work' => $info['available_for_work'],
                'years_of_exp' => $info['years_of_exp'],
                'completed_projects' => $info['completed_projects'],
                'client_satisfaction' => $info['client_satisfaction'],
                'open_source_contributions' => $info['open_source_contributions'],
                'avatar' => $info['avatar'],
                'secondary_avatar' => $info['secondary_avatar'],
            ]
        );
    }
}
