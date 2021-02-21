<?php


namespace App\Traits;

use Cocur\Slugify\Slugify;

Trait SlugifyTrait
{

    public function slugify(string $string_to_slug): string
    {
        $slugify = new Slugify();
        return $slugify->slugify($string_to_slug);
    }
}