<?php

namespace App\Tests;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

class VideoPantherTest extends PantherTestCase
{
    public function testVideoPantherValidForm(): void
    {
        // Lance Chrome (Panther WebDriver)
        $client = static::createPantherClient([
            'browser' => static::CHROME,
            'browser_arguments' => [
                '--headless', // Run in headless mode ou mode sans interface
                '--no-sandbox', // Required if running as root in Docker, nécessaire si tu exécutes en root (Docker)
                '--disable-dev-shm-usage', // corrige les problèmes de mémoire partagée
                '--disable-gpu',
            ]
        ]);

        // Ouvre la page d’accueil (pas de paramètres serveur ici)
        $crawler = $client->request('GET', 'http://nginx');

        $client->followRedirects();

        // Vérifie le titre de la page
        $this->assertPageTitleSame('Vidéos');

        // Sélectionne le formulaire SANS cliquer de bouton
        $form = $crawler->filter('form[name="video"]')->form();
        // $form = $crawler->selectButton('Enregistrer')->form();

        // Remplit les champs texte
        $form['video[title]'] = 'Test Video';
        $form['video[description]'] = 'Description du vidéo';
        $form['video[visibility]'] = '1';

        $thumbnailPath = __DIR__.'/files/fanadiovana.jpg';
        $videoPath     = __DIR__.'/files/rija.mp4';

        $this->assertFileExists($thumbnailPath);
        $this->assertFileExists($videoPath);

        // Upload des fichiers (Important : les chemins doivent exister DANS le conteneur php-fpm)
        /** @var FileFormField $form['video[thumbnail]']  */
        $form['video[thumbnail]']->upload($thumbnailPath);
        /** @var FileFormField $form['video[videoFile]']  */
        $form['video[videoFile]']->upload($videoPath);

        // Soumet le formulaire
        $client->submit($form);

        $driver = $client->getWebDriver();

        $wait = new WebDriverWait($driver, 10); // wait up to 10 seconds

        // Vérifie que le poster de la vidéo est de la forme "/upload/thumbnails/69202d61abaa93.96087231.jpg"
        $video = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('#videos_list video')
            )
        );

        $poster = $video->getAttribute('poster');

        $this->assertMatchesRegularExpression(
            '/\/upload\/thumbnails\/[A-Za-z0-9]+\.[0-9]+\.jpg$/',
            $poster
        );

        // Vérifie que la vidéo est de la forme "/upload/videos/69202d61acc1c6.83134536.mp4"
        $source = $wait->until(
            WebDriverExpectedCondition::presenceOfElementLocated(
                WebDriverBy::cssSelector('#videos_list video source')
            )
        );

        $src = $source->getAttribute('src');

        $this->assertMatchesRegularExpression(
            '/\/upload\/videos\/[A-Za-z0-9]+\.[0-9]+\.mp4$/',
            $src
        );

    }
}
