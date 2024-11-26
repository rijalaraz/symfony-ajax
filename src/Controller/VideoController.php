<?php

namespace App\Controller;

use App\Entity\Video;
use App\Entity\Visibility;
use App\Form\VideoType;
use App\Repository\VideoRepository;
use App\Service\VideoService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VideoController extends AbstractController
{
    #[Route('/', name: 'app_video')]
    public function index(Request $request, VideoRepository $videoRepository, VideoService $videoService, ParameterBagInterface $parameters, EntityManagerInterface $em): Response
    {
        // $videoForm = $this->createForm(VideoType::class, $videoRepository->new());
        // $videoForm->handleRequest($request);

        //if ($videoForm->isSubmitted()) {
            // return $videoService->handleVideoForm($videoForm);
        // }

        if ($request->isXMLHttpRequest()) {

            /** @var UploadedFile $videoFile */
            $videoFile = $request->files->get('videoFile');
            /** @var UploadedFile $thumbnail */
            $thumbnail = $request->files->get('thumbnail');

            $video = new Video();
            $video->setTitle($request->request->get('title'));
            $video->setDescription($request->request->get('description'));

            if($request->request->get('visibility') == Visibility::PUBLIC) {
                $visibility = $em->find(Visibility::class, Visibility::PUBLIC);
            } elseif ($request->request->get('visibility') == Visibility::PRIVATE) {
                $visibility = $em->find(Visibility::class, Visibility::PRIVATE);
            }

            $video->setVisibility($visibility);

            if(!is_null($videoFile)) {
                $videoFilename = uniqid().".".$videoFile->getClientOriginalExtension();
                $path = $parameters->get('videos.upload_directory');
                $videoFile->move($path, $videoFilename);

                $video->setVideoFile($videoFilename);
            }

            if(!is_null($thumbnail)) {
                $thumbnailFilename = uniqid().".".$thumbnail->getClientOriginalExtension();
                $path = $parameters->get('thumbnails.upload_directory');
                $thumbnail->move($path, $thumbnailFilename);

                $video->setThumbnail($thumbnailFilename);
            }

            $em->persist($video);
            $em->flush();

            return new JsonResponse([
                'code' => Video::VIDEO_ADDED_SUCCESSFULLY,
                'html' => ''
            ]);

            return new JsonResponse($status);
        }

        return $this->render('video/index.html.twig');
    }
}
