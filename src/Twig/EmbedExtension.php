<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class EmbedExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('getEmbedThumbnail', [$this, 'getEmbedThumbnail'])
        ];
    }

    public function getEmbedThumbnail($html): string
    {
        if ($this->isYoutube($html)) {
            return $this->getYoutubeThumbnail($this->getYoutubeId($html));
        }

        return "https://placehold.co/600x400?text=Thumbnail+Error";
    }

    private function isYoutube($html): bool
    {
        $pattern = '/https?:\/\/(?:www\.)?youtube\.com\/embed\/[\w-]+/i';
        preg_match($pattern, $html, $matches);

        return !empty($matches);
    }

    private function getYoutubeId($iframe): string
    {
        preg_match('/src="([^"]+)"/', $iframe, $matches);
        $srcAttribute = $matches[1] ?? '';

        preg_match('/embed\/([a-zA-Z0-9_-]+)/', $srcAttribute, $videoIdMatches);
        $videoId = $videoIdMatches[1] ?? '';

        return $videoId;
    }

    private function getYoutubeThumbnail($id): string
    {
        return "https://img.youtube.com/vi/$id/0.jpg";
    }
}