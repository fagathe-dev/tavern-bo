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

    /**
     * upload
     *
     * @param  UploadedFile $file
     * @param  string $targetDir
     * @return void
     */
    public function upload(UploadedFile $file, string $targetDir = '')
    {
        $targetDir = $this->getBaseDir() . DIRECTORY_SEPARATOR . $targetDir;
        dd($targetDir);
    }

    /**
     * checkMimetype
     *
     * @param  UploadedFile $file
     * @return bool
     */
    private function checkMimetype(UploadedFile $file): bool
    {
        return false;
    }

    /**
     * chackMaxFileSize
     *
     * @param  UploadedFile $file
     * @return bool
     */
    private function chackMaxFileSize(UploadedFile $file): bool
    {
        return false;
    }

    /**
     * generateDir
     *
     * @param  string $dir
     * @return void
     */
    private function generateDir(?string $dir = ''): void
    {
        if ($this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }

    /**
     * remove
     *
     * @param  string $path
     * @return void
     */
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