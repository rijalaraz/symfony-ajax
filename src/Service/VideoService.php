<?php

namespace App\Service;

use Symfony\Component\Form\FormInterface;
use App\Entity\Video;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

class VideoService
{
    public function __construct(
        private EntityManagerInterface $em,
        private ParameterBagInterface $parameters,
        private Environment $environment,
    ) {}

    public function handleVideoForm(FormInterface $videoForm): JsonResponse
    {
        if ($videoForm->isValid()) {
            return $this->handleValidForm($videoForm);
        } else {
            return $this->handleInvalidForm($videoForm);
        }
    }

    public function handleValidForm(FormInterface $videoForm): JsonResponse
    {
        /** @var Video $video */
        $video = $videoForm->getData();

        /** @var UploadedFile $uploadedThumbnail */
        $uploadedThumbnail = $videoForm['thumbnail']->getData();

        $uploadedVideo = $videoForm['videoFile']->getData();

        if ($uploadedThumbnail) {
            $newFileName = $this->renameUploadedFile($uploadedThumbnail, $this->parameters->get('thumbnails.upload_directory'));
            $video->setThumbnail($newFileName);
        }

        $newFileName = $this->renameUploadedFile($uploadedVideo, $this->parameters->get('videos.upload_directory'));
        $video->setVideoFile($newFileName);

        $this->em->persist($video);
        $this->em->flush();

        return new JsonResponse([
            'code' => Video::VIDEO_ADDED_SUCCESSFULLY,
            'html' => $this->environment->render('video/video.html.twig', [
                'video' => $video
            ])
        ]);
    }

    public function handleInvalidForm(FormInterface $videoForm): JsonResponse
    {
        return new JsonResponse([
            'code' => Video::VIDEO_INVALID_FORM,
            'errors' => $this->getErrorsMessages($videoForm)
        ]);
    }

    private function renameUploadedFile(UploadedFile $uploadedFile, string $directory): string
    {
        $newFileName = uniqid(more_entropy: true). ".{$uploadedFile->guessExtension()}";
        $uploadedFile->move($directory, $newFileName);

        return $newFileName;
    }

    private function getErrorsMessages(FormInterface $form): array
    {
        $errors = [];

        foreach ($form->all() as $child) {
            foreach ($child->getErrors() as $error) {
                $name = $child->getName();
                $errors[$name] = $error->getMessage();
            }
        }

        return $errors;
    }
}