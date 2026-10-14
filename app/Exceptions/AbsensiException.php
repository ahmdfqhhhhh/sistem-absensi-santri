<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AbsensiException extends Exception
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [];

    public function __construct(string $message = '', int $code = Response::HTTP_UNPROCESSABLE_ENTITY, array $data = [])
    {
        parent::__construct($message, $code);
        $this->data = $data;
    }

    /**
     * Get additional context data.
     *
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->getMessage(),
        ];

        if (! empty($this->data)) {
            $response['data'] = $this->data;
        }

        return response()->json($response, $this->getCode() ?: Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
