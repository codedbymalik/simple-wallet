<?php

namespace Core;

use JsonException;

abstract class BaseController
{
    protected function sendJson(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);
        } catch (JsonException $e) {
            error_log("JSON encoding error: " . $e->getMessage());
            echo json_encode(['error' => 'Internal server error']);
        }
        exit;
    }

    protected function getJsonPayload(): array
    {
        try {
            $input = file_get_contents('php://input');
            $data = json_decode($input, true, 512, JSON_THROW_ON_ERROR);
            return $data ?? [];
        } catch (JsonException $e) {
            error_log("JSON parsing error: " . $e->getMessage());
            return [];
        }
    }

    protected function sendError(string $message, int $statusCode = 400): void
    {
        $this->sendJson(['error' => $message], $statusCode);
    }

    protected function sendSuccess(array $data = [], string $message = 'Success', int $statusCode = 200): void
    {
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
        $this->sendJson($response, $statusCode);
    }
}
