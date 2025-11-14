<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

class VideoTest extends PantherTestCase
{
    public function testVideoInvalidForm(): void
    {
        // Lance Chrome (Panther WebDriver)
        // Create a Chrome WebDriver client
        $client = static::createPantherClient([
            'browser' => static::CHROME,
            // 'capabilities' => [
            //     'goog:chromeOptions' => [
            //         'binary' => '/usr/bin/google-chrome', // Chemin dans ton conteneur Docker
            //         'args' => [
            //             '--headless', // Run in headless mode ou mode sans interface
            //             '--no-sandbox', // Required if running as root in Docker, nécessaire si tu exécutes en root (Docker)
            //             '--disable-dev-shm-usage', // corrige les problèmes de mémoire partagée
            //             '--disable-gpu',
            //             // '--no-zygote',
            //             // '--disable-software-rasterizer',
            //         ],
            //     ],
            // ],
        ]);

        // Ouvre la page d’accueil (pas de paramètres serveur ici)
        $crawler = $client->request('GET', 'http://nginx');

        $client->followRedirects();

        // Vérifie le titre de la page
        $this->assertPageTitleSame('Vidéos');

        // Sélectionne le formulaire
        $form = $crawler->selectButton('Enregistrer')->form([
            'video[title]' => 'Test Video',
            'video[description]' => 'Description du vidéo',
            'video[visibility]' => '1',
        ]);

        // Attache les fichiers à uploader (dans le conteneur Docker)
        // Important : les chemins doivent exister DANS le conteneur php-fpm
        /** @var FileFormField $form['video[thumbnail]']  */
        $form['video[thumbnail]']->upload('/home/fanadiovana.jpg');
        /** @var FileFormField $form['video[videoFile]']  */
        $form['video[videoFile]']->upload('/home/rija.mp4');

        // Soumet le formulaire
        $client->submit($form);


        // Crée des fichiers uploadés (doivent exister dans le conteneur)
        // $thumbnail = new UploadedFile('/home/fanadiovana.jpg', 'fanadiovana.jpg', 'image/jpeg', null, true);
        // $videoFile = new UploadedFile('/home/rija.mp4', 'rija.mp4', 'video/mp4', null, true);

        // // Send AJAX request
        // $client->xmlHttpRequest('POST', '/', [
        //     'video' => [
        //         'title' => 'Test Video',
        //         'description' => 'Description du vidéo',
        //         'visibility' => '1',
        //     ]
        // ], [
        //     'video' => [
        //         'thumbnail' => $thumbnail,
        //         'videoFile' => $videoFile
        //     ]
        // ]);

        // $this->assertResponseIsSuccessful();
        // $responseData = json_decode($client->getResponse()->getContent(), true);

        // $this->assertArrayHasKey('success', $responseData);
        // $this->assertTrue($responseData['success']);


        // $this->assertResponseIsSuccessful();
        // $this->assertResponseStatusCodeSame(200);

    }
}
