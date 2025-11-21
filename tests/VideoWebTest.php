<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\DomCrawler\Field\FileFormField;

class VideoWebTest extends WebTestCase
{
    public function testVideoAjaxForm(): void
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/');

        $this->assertPageTitleSame('Vidéos');

        $thumbnailPath = __DIR__.'/files/fanadiovana.jpg';
        $videoPath     = __DIR__.'/files/rija.mp4';

        $this->assertFileExists($thumbnailPath);
        $this->assertFileExists($videoPath);

        // Check the submit button really exists
        $this->assertSelectorExists('button:contains("Enregistrer")');

        $form = $crawler->filter('form[name="video"]')->form();
        // $form = $crawler->selectButton('Enregistrer')->form();

        // Fill fields
        $form['video[title]'] = 'Test Video';
        $form['video[description]'] = 'Description du vidéo';
        $form['video[visibility]'] = '1';

        // Upload files
        /** @var FileFormField $form['video[thumbnail]']  */
        $form['video[thumbnail]']->upload($thumbnailPath);
        /** @var FileFormField $form['video[videoFile]']  */
        $form['video[videoFile]']->upload($videoPath);

        // Send Ajax Form
        $client->xmlHttpRequest('POST', '/', $form->getPhpValues(), $form->getPhpFiles());

        // Crée des fichiers uploadés (doivent exister dans le conteneur)
        // $thumbnail = new UploadedFile($thumbnailPath, 'fanadiovana.jpg', 'image/jpeg', null, true);
        // $videoFile = new UploadedFile($videoPath, 'rija.mp4', 'video/mp4', null, true);

        // Send AJAX request
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

        // Vérifie que la requête AJAX a réussi
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        // Récupère les fichiers reçus par Symfony
        $receivedFiles = $client->getRequest()->files->all();

        // Vérifie que Symfony les a bien reçus
        $this->assertArrayHasKey('video', $receivedFiles);
        $this->assertInstanceOf(UploadedFile::class, $receivedFiles['video']['thumbnail']);
        $this->assertInstanceOf(UploadedFile::class, $receivedFiles['video']['videoFile']);

        // Vérifie le bon type MIME
        // $this->assertSame('image/jpeg', $receivedFiles['video']['thumbnail']->getClientMimeType());
        // $this->assertSame('video/mp4',  $receivedFiles['video']['videoFile']->getClientMimeType());

    }
}
