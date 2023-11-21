<?php
namespace App\Service\Uploader;

class UploadError
{

    public const UPLOAD_ERROR = 'upload_error';
    public const UPLOAD_MIME_TYPE_ERROR = 'upload_mime_type_error';
    public const UPLOAD_SIZE_ERROR = 'upload_size_error';
    public const UPLOAD_SAVE_ERROR = 'upload_save_error';
    public const UPLOAD_TARGET_ERROR = 'upload_target_error';

    private string $code = self::UPLOAD_ERROR;
    private string $type = "";
    private string $message = "";

    /**
     * Get the value of code
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get the value of type
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Get the value of message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */
    public function setMessage($message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Set the value of type
     *
     * @return  self
     */
    public function setType($type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */
    public function setCode($code): self
    {
        $this->code = $code;

        return $this;
    }
}