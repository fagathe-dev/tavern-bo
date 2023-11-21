<?php
namespace App\Service\Uploader;

use App\Utils\ServiceTrait;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Uploader
{

    use ServiceTrait;

    private Filesystem $fs;
    private bool $error = false;
    private array $errors = [];

    public function __construct(
        private ParameterBagInterface $parameters,
    ) {
        $this->fs = new Filesystem();
    }

    /**
     * getBaseDir
     *
     * @return string
     */
    public function getBaseDir(): string
    {
        return $this->parameters->get('uploads_directory') ?? '';
    }

    public function upload(UploadedFile $file, string $targetDir = '')
    {
        $targetDir = $this->getBaseDir() . DIRECTORY_SEPARATOR . $targetDir;
        dd($targetDir);
    }

    private function checkMimetype(string $mimetype): bool
    {
        return false;
    }

    private function chackMaxFileSize(): bool
    {
        return false;
    }

    private function generateDir(?string $dir = ''): void
    {
    }

    public function remove(?string $path = ''): void
    {
    }

    /**
     * setError
     *
     * @param bool|null $error
     * @return self
     */
    public function setError(?bool $error = null): self
    {
        $this->error = $error ?? false;

        return $this;
    }

    public function getError(): ?bool
    {
        return $this->error;
    }

    /**
     * hasError
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return count($this->getErrors()) > 0;
    }

    /**
     * setErrors
     *
     * @param  UploadError $error
     * @return self
     */
    public function setErrors(UploadError $error): self
    {
        $this->errors = [...$this->errors, $error];

        return $this;
    }

    /**
     * @return UploadError[] 
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

}