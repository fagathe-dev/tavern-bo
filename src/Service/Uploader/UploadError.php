<?php
namespace App\Service\Uploader;

class UploadError
{

    public const UPLOAD_NO_CONTENT = 'UPLOAD_NO_CONTENT';
    public const UPLOAD_ERROR = 'UPLOAD_ERROR';
    public const UPLOAD_MIME_TYPE_ERROR = 'UPLOAD_MIME_TYPE_ERROR';
    public const UPLOAD_SIZE_ERROR = 'UPLOAD_SIZE_ERROR';
    public const UPLOAD_SAVE_ERROR = 'UPLOAD_SAVE_ERROR';
    public const UPLOAD_TARGET_ERROR = 'UPLOAD_TARGET_ERROR';
    public const UPLOAD_ERROR_SIZE = 'UPLOAD_ERROR_SIZE';


    public function __construct(private string $message, private string $type, private string $code = self::UPLOAD_ERROR)
    {
    }

    /**
     * Get the value of code
     */
    public function getCode(): string
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