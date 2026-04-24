<?php

namespace App\Core\Exceptions;

use RuntimeException;

final class ApiException extends RuntimeException
{
    /**
     * @param array<string, mixed> $details
     */
    public function __construct(
        public readonly string $errorCode,
        string $message,
        public readonly int $status = 400,
        public readonly array $details = [],
    ) {
        parent::__construct($message);
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function unprocessable(string $errorCode, string $message, array $details = []): self
    {
        return new self($errorCode, $message, 422, $details);
    }

    public static function unauthorized(string $message = 'Unauthorized'): self
    {
        return new self(ErrorCode::UNAUTHORIZED->value, $message, 401);
    }

    /**
     * @param array<string, mixed> $details
     */
    public static function badRequest(string $errorCode, string $message, array $details = []): self
    {
        return new self($errorCode, $message, 400, $details);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}

