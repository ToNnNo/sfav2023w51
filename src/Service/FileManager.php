<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

readonly class FileManager
{

    public function __construct(
        private SluggerInterface      $slugger,
        private ParameterBagInterface $parameterBag,
        private Filesystem            $filesystem
    )
    {
    }

    public function upload(UploadedFile $file): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = $this->slugger->slug($originalName);
        $filename = $newFilename . (new \DateTime())->format('Ymdhis') . "." . $file->guessClientExtension();

        $file->move(
            $this->parameterBag->get('profile_directory'),
            $filename
        );

        return $filename;
    }

    public function remove(string $filename): bool
    {
        $pathFile = $this->parameterBag->get('profile_directory') . $filename;

        if ($this->filesystem->exists($pathFile)) {
            $this->filesystem->remove($pathFile);
            return true;
        }

        return false;
    }

}
