<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class VideoWebTest extends WebTestCase
{
    public function testVideoAjaxForm(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $client->followRedirects();

        // Vérifie le titre de la page
        $this->assertPageTitleSame('Vidéos');

        // Crée des fichiers uploadés (doivent exister dans le conteneur)
        $thumbnail = new UploadedFile('/home/fanadiovana.jpg', 'fanadiovana.jpg', 'image/jpeg', null, true);
        $videoFile = new UploadedFile('/home/rija.mp4', 'rija.mp4', 'video/mp4', null, true);

        // Send AJAX request
        $client->xmlHttpRequest('POST', '/', [
            'video' => [
                'title' => 'Test Video',
                'description' => 'Description du vidéo',
                'visibility' => '1',
            ]
        ], [
            'video' => [
                'thumbnail' => $thumbnail,
                'videoFile' => $videoFile
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);
    }
}
