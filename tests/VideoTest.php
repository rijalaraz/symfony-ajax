<?php

namespace App\Tests;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Panther\PantherTestCase;

class VideoTest extends PantherTestCase
{
    public function testVideoInvalidForm(): void
    {
        $client = static::createPantherClient(['browser' => static::FIREFOX]);
        $crawler = $client->request('GET', '/');

        $client->followRedirects();

        $this->assertPageTitleSame('Vidéos');

        $thumbnail = new UploadedFile('/home/sakafo1.jpg', 'sakafo1.jpg', 'image/jpeg');

        $videoFile = new UploadedFile('/home/web_video.webm', 'web_video.webm', 'video/webm');

        $client->xmlHttpRequest('POST', '/', [
            'video[title]' => 'Titre',
            'video[description]' => 'Description du vidéo',
            'video[visibility]' => '1'
        ], [
            'video[thumbnail]' => $thumbnail,
            'video[videoFile]' => $videoFile
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

    }
}
