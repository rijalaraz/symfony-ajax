<?php

namespace App\Tests;

trait VideoAssertionsTrait
{
    /**
     * Assert that at least $count videos exist on the page.
     */
    public function assertVideosExist(int $count = 1, string $selector = 'video', int $timeout = 5): void
    {
        /** @var \Symfony\Component\Panther\Client $client */
        $client = static::getClient();

        // Wait for at least one video to appear
        $client->waitFor($selector, $timeout);

        $videos = $client->getCrawler()->filter($selector);
        $this->assertGreaterThanOrEqual($count, $videos->count(), "Expected at least {$count} videos on the page.");
    }

    /**
     * Assert that all video <source> elements match a regex pattern.
     */
    public function assertVideoSourcesMatch(string $videoSourcePattern, string $videoPosterPattern, string $videoSelector = 'video', int $timeout = 5): void
    {
        /** @var \Symfony\Component\Panther\Client $client */
        $client = static::getClient();

        // Wait for videos to appear
        $client->waitFor($videoSelector, $timeout);

        $videos = $client->getCrawler()->filter($videoSelector);

        $videos->each(function ($videoElement, $i) use ($videoSourcePattern, $videoPosterPattern) {

            // Vérifie que le poster de la vidéo est de la forme "/upload/thumbnails/69202d61abaa93.96087231.jpg"
            $poster = $videoElement->attr('poster');

            $this->assertMatchesRegularExpression(
                $videoPosterPattern,
                $poster,
                "Video #{$i} poster '{$poster}' does not match pattern '{$videoPosterPattern}'"
            );

            // Vérifie que la vidéo est de la forme "/upload/videos/69202d61acc1c6.83134536.mp4"
            $source = $videoElement->filter('source')->first();
            $src = $source->attr('src');
            $this->assertMatchesRegularExpression(
                $videoSourcePattern,
                $src,
                "Video #{$i} source '{$src}' does not match pattern '{$videoSourcePattern}'"
            );
        });
    }
}
