<?php
namespace App\Service\Uploader;

use App\Helpers\FileHelperTrait;
use App\Service\Token\TokenGenerator;
use App\Utils\ServiceTrait;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class Uploader {

    use ServiceTrait;
    use FileHelperTrait;

    private Filesystem $fs;
    private array $errors = [];
    private string $uploadDir = '';

    private string $fileName = '';
    private string $uploadPath = '';

    public function __construct(
        private ParameterBagInterface $parameters,
        private TokenGenerator $tokenGenerator,
        private LoggerInterface $logger
    ) {
        $this->fs = new Filesystem();
    }

    /**
     * getBaseDir
     *
     * @return string
     */
    public function getBaseDir(): string {
        return ($this->parameters->get('root_directory').$this->parameters->get('uploads_directory')) ?? '';
    }

    /**
     * upload
     *
     * @param  UploadedFile $file
     * @param  string $targetDir
     * @return self
     */
    public function upload(?UploadedFile $file, ?array $options = []): self {
        $targetDir = $options['targetDir'] ?? '';
        $fileType = $options['fileType'] ?? '';
        $renamed = $options['renamed'] ?? true;

        if($file === null) {
            $error = new UploadError('Aucun fichier reçu', 'file', UploadError::UPLOAD_NO_CONTENT);
            $this->setErrors($error);
            $this->logger->error('{code} ::: {type} ::: {message}', [
                'code' => $error->getCode(),
                'type' => $error->getType(),
                'message' => $error->getMessage(),
            ]);

            return $this;
        }

        if($this->checkMaxFileSize($file, $this->getMaxFileSize())) {
            $error = new UploadError(message: 'Fichier trop lourd', type: 'fileSize', code: UploadError::UPLOAD_ERROR_SIZE);
            $this->setErrors($error);
            $this->logger->error('{code} ::: {type} ::: {message}', [
                'code' => $error->getCode(),
                'type' => $error->getType(),
                'message' => $error->getMessage(),
            ]);
        }

        if($fileType && !in_array($file->getMimeType(), UploadMimeType::getDocumentMimeType($fileType))) {
            $error = new UploadError(
                message: 'Fichier ".'.$file->guessClientExtension().'" reçu, seuls les fichiers avec les extensions "'.join(', ', UploadMimeType::getMimeTypeExtensions($fileType)).'" sont acceptés',
                type: 'fileType',
                code: UploadError::UPLOAD_MIME_TYPE_ERROR
            );
            $this->setErrors($error);
            $this->logger->error('{code} ::: {type} ::: {message}', [
                'code' => $error->getCode(),
                'type' => $error->getType(),
                'message' => $error->getMessage(),
            ]);
        }

        if($this->hasErrors()) {
            return $this;
        }

        $renamed ? $this->generateFileName($file) : $this->setFileName($file->getClientOriginalName());

        $this->setUploadDir($targetDir)
            ->setUploadPath($targetDir)
        ;

        try {
            $file->move($this->getUploadDir(), $this->getFileName());
        } catch (FileException $e) {
            $this->logger->error('Une erreur est survenue lors de l\'enregistrement du fichier dans le dossier {dir} :::: {message}', [
                'dir' => $this->getUploadDir(),
                'message' => $e->getMessage(),
            ]);
        } catch (Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return $this;
    }

    /**
     * generateFileName
     *
     * @param  mixed $file
     * @return self
     */
    private function generateFileName(UploadedFile $file): self {
        return $this->setFileName(
            str_replace('.', '', $this->tokenGenerator->generate(length: 40, unique: true))
            .'.'.$file->guessClientExtension()
        );
    }

    /**
     * checkMimetype
     *
     * @param  UploadedFile $file
     * @return bool
     */
    private function checkMimetype(UploadedFile $file): bool {
        return false;
    }

    /**
     * getMaxFileSize
     *
     * @return float
     */
    private function getMaxFileSize(): float {
        return (float)$this->parameters->get('uploads_max_file_size');
    }


    /**
     * chackMaxFileSize
     *
     * @param  UploadedFile $file
     * @param 
     * @return bool
     */
    private function checkMaxFileSize(UploadedFile $file, ?int $maxFileSize = null): bool {
        if($maxFileSize) {
            return $maxFileSize > $file->getSize();
        }

        return $this->getMaxFileSize() > $file->getSize();
    }

    /**
     * generateDir
     *
     * @param  string $dir
     * @return void
     */
    private function generateDir(?string $dir = ''): void {
        if($this->fs->exists($dir)) {
            $this->fs->mkdir($dir);
        }
    }

    /**
     * remove
     *
     * @param  string $path
     * @return void
     */
    public function remove(?string $path = ''): void {
        if($path !== null && $this->fs->exists($path)) {
            $this->fs->remove($path);
        }
    }

    /**
     * hasError
     *
     * @return bool
     */
    public function hasErrors(): bool {
        return count($this->getErrors()) > 0;
    }

    /**
     * setErrors
     *
     * @param  UploadError $error
     * @return self
     */
    public function setErrors(UploadError $error): self {
        $this->errors = [...$this->errors, $error];

        return $this;
    }

    /**
     * @return UploadError[] 
     */
    public function getErrors(): array {
        return $this->errors;
    }


    /**
     * Get the value of uploadDir
     */
    public function getUploadDir(): string {
        return $this->uploadDir;
    }

    /**
     * Set the value of uploadDir
     *
     * @return self
     */
    public function setUploadDir(string $uploadDir): self {
        $this->uploadDir = $this->getBaseDir().$uploadDir;

        return $this;
    }

    /**
     * Get the value of fileName
     *
     * @return string
     */
    public function getFileName(): string {
        return $this->fileName;
    }

    /**
     * Set the value of fileName
     *
     * @return  self
     */
    public function setFileName(string $fileName): self {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * Get the value of uploadPath
     */
    public function getUploadPath(): string {
        return $this->uploadPath;
    }

    /**
     * Set the value of uploadPath
     *
     * @param string $uploadPath
     * @return  self
     */
    public function setUploadPath(string $uploadPath): self {
        $this->uploadPath = $uploadPath.DIRECTORY_SEPARATOR.$this->getFileName();

        return $this;
    }
}