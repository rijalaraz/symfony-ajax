<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

class VideoPantherTest extends PantherTestCase
{
    public function testVideoPantherForm(): void
    {
        // Lance Chrome (Panther WebDriver)
        // Create a Chrome WebDriver client
        $client = static::createPantherClient([
            'browser' => static::CHROME,
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

        $thumbnailPath = __DIR__.'/files/fanadiovana.jpg';
        $videoPath     = __DIR__.'/files/rija.mp4';

        $this->assertFileExists($thumbnailPath);
        $this->assertFileExists($videoPath);

        // Attache les fichiers à uploader (dans le conteneur Docker)
        // Important : les chemins doivent exister DANS le conteneur php-fpm
        /** @var FileFormField $form['video[thumbnail]']  */
        $form['video[thumbnail]']->upload($thumbnailPath);
        /** @var FileFormField $form['video[videoFile]']  */
        $form['video[videoFile]']->upload($videoPath);

        // Soumet le formulaire
        $client->submit($form);

    }
}
