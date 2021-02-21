<?php

namespace App\Twig;

use App\Entity\Post;
use App\Entity\User;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('exceptTheLatestPost', [$this, 'filterPosts']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('exceptTheLatestPost', [$this, 'filterPosts']),
            new TwigFunction('latestPostId', [$this, 'latestPostId']),
            new TwigFunction('pluralize', [$this, 'pluralize']),
            new TwigFunction('getUserTitle', [$this, 'getUserTitle']),
            new TwigFunction('date_difference', [$this, 'date_difference']),
        ];
    }

    public function filterPosts(array $posts): array
    {
        unset($posts[0]);
        return $posts;
    }

    public function latestPostId(array $posts): int
    {
        /** @var Post $post */
        $post = $posts[count($posts) - 1];
        return $post->getId();
    }

    public function pluralize($count, $name): string
    {
        if ( $name === 'Post' ) {
            return $count === 1 ? $count . ' ' . $name : $count . ' ' . $name . 's';
        }
        return $count === 1 ? $count . ' Category' : $count . ' Categories';
    }

    public function getUserTitle(string $__gender): ?string
    {
        foreach (User::GENDERS as $k => $v) {
            if ($v === $__gender) return ucfirst($k);
        }
        return null;
    }

    public function date_difference($createdAt): string
    {
        $now = new \DateTimeImmutable();
        $diff = $now->diff($createdAt);
        $string = null;
        if ($diff->h === 0) {
            if ($diff->i === 0) {
                $string = $diff->s . ' scs';
            }
            else {
                $minutes = $diff->i;
                if ($minutes > 1) {
                    $string = $diff->i . ' mins';
                }
                else {
                    $string = $diff->i . ' min';
                }
            }
        }
        else {
            if ($diff->d === 0) {
                if ($diff->h > 1 ) {
                    $string = $diff->h . ' hrs';
                }
                else {
                    $string = $diff->h . ' hr';
                }
            }
            elseif ($diff->d >= 1) {
                if ($diff->d > 1) {
                    $string = $diff->d . ' days';
                }
                else {
                    $string = $diff->d . ' day';
                }
            }
            elseif ($diff->m >= 1) {
                if ($diff->m > 1) {
                    $string = $diff->d . ' months';
                }
                else {
                    $string = $diff->d . ' month';
                }
            }
            elseif ($diff->y >= 1) {
                if ($diff->y > 1) {
                    $string = $diff->d . ' years';
                }
                else {
                    $string = $diff->d . ' year';
                }
            }

        }
        return $string;
    }
}
