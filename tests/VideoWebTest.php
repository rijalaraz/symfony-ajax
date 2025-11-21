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

        // Vérifie que la requête AJAX a réussi
        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(200);

        $this->assertResponseHeaderSame('content-type', 'application/json');

        // Optionally assert JSON structure
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('code', $data);

        switch ($data['code']) {
            case 'VIDEO_ADDED_SUCCESSFULLY':

                // Vérifie que le poster de la vidéo est de la forme "/upload/thumbnails/69202d61abaa93.96087231.jpg"
                $this->waitFor(function () use (&$crawler, $client) {
                    return $crawler->filter('#videos_list video')->count() > 0;
                });

                $poster = $crawler->filter('#videos_list video')->attr('poster');

                $this->assertMatchesRegularExpression(
                    '/\/upload\/thumbnails\/[A-Za-z0-9]+\.[0-9]+\.jpg$/',
                    $poster
                );

                // Vérifie que la vidéo est de la forme "/upload/videos/69202d61acc1c6.83134536.mp4"
                $this->waitFor(function () use (&$crawler, $client) {
                    return $crawler->filter('#videos_list video source')->count() > 0;
                });

                $src = $crawler->filter('#videos_list video source')->attr('src');

                $this->assertMatchesRegularExpression(
                    '/^\/upload\/videos\/[a-z0-9]+\.[0-9]+\.mp4$/',
                    $src
                );

                // Récupère les fichiers reçus par Symfony
                $receivedFiles = $client->getRequest()->files->all();

                // Vérifie que Symfony les a bien reçus
                $this->assertArrayHasKey('video', $receivedFiles);
                $this->assertInstanceOf(UploadedFile::class, $receivedFiles['video']['thumbnail']);
                $this->assertInstanceOf(UploadedFile::class, $receivedFiles['video']['videoFile']);

                // Vérifie le bon type MIME
                // $this->assertSame('image/jpeg', $receivedFiles['video']['thumbnail']->getClientMimeType());
                // $this->assertSame('video/mp4',  $receivedFiles['video']['videoFile']->getClientMimeType());
                break;
            
            default:
                # code...
                break;
        }

    }

    private function waitFor(callable $fn, int $timeoutMs = 3000, int $intervalMs = 100): void
    {
        $start = microtime(true);

        while (true) {
            if ($fn()) {
                return; // Condition met
            }

            if ((microtime(true) - $start) * 1000 > $timeoutMs) {
                $this->fail("Timeout waiting for condition");
            }

            usleep($intervalMs * 1000);
        }
    }


}
