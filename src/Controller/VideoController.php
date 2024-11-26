<?php

namespace App\Controller;

use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Service\VideoService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoController extends AbstractController
{
    #[Route('/', name: 'app_video')]
    public function index(Request $request, VideoRepository $videoRepository, VideoService $videoService): Response
    {
        $videoForm = $this->createForm(VideoType::class, $videoRepository->new());

        $videoForm->handleRequest($request);

        if ($videoForm->isSubmitted() && $request->isXMLHttpRequest()) {
            return $videoService->handleVideoForm($videoForm);
        }

        return $this->render('video/index.html.twig', [
            'videoform' => $videoForm->createView()
        ]);
    }
}
