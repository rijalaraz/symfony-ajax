<?php

namespace App\Tests;

use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;

class VideoPantherTest extends PantherTestCase
{
    use VideoAssertionsTrait;

    public function testVideoPantherValidForm(): void
    {
        // Lance Chrome (Panther WebDriver)
        $client = static::createPantherClient([
            'browser' => static::CHROME,
            'browser_arguments' => [
                '--no-sandbox', // Required in Docker (root user)
                '--disable-dev-shm-usage', // Fix Chrome /dev/shm issues (shared memory)
                '--disable-gpu', // No GPU in Docker
                '--disable-software-rasterizer', // Prevent fallback GPU rendering (crashes)
                '--disable-extensions',
                '--disable-notifications', // Avoid DBus warnings
                '--disable-default-apps', // More stable in CI
                '--disable-features=VizDisplayCompositor', // Avoid rendering bugs / blank pages
                '--headless=new', // Faster and more stable than old --headless, remove if using debug=true
            ],
            // 'debug' => true, // Enable this to keep window open and disable headless on failure
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

        // Assert at least 2 videos exist
        $this->assertVideosExist(2);

        // Assert all video sources end with .mp4
        $this->assertVideoSourcesMatch('/\/upload\/videos\/[A-Za-z0-9]+\.[0-9]+\.mp4$/', '/\/upload\/thumbnails\/[A-Za-z0-9]+\.[0-9]+\.jpg$/');

    }
}
