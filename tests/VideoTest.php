<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Panther\PantherTestCase;

class VideoTest extends WebTestCase
{
    public function testVideoInvalidForm(): void
    {
        $client = static::createClient();

        // Accède à la page d’accueil
        $crawler = $client->request('GET', '/');

        $client->followRedirects();

        $this->assertPageTitleSame('Vidéos');

        // Crée des fichiers uploadés (doivent exister dans le conteneur)
        $thumbnail = new UploadedFile('/home/fanadiovana.jpg', 'fanadiovana.jpg', 'image/jpeg');

        $videoFile = new UploadedFile('/home/rija.mp4', 'rija.mp4', 'video/mp4');

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

        // $this->assertResponseIsSuccessful();
        // $responseData = json_decode($client->getResponse()->getContent(), true);

        // $this->assertArrayHasKey('success', $responseData);
        // $this->assertTrue($responseData['success']);


        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

    }
}
