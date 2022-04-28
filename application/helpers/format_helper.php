<?php

function formatResponse(int $status, array $data = [], array $errorFrom = [], string $errorMessage = '', array $successForm = [], string $successMessage = ''): array
{
    $res = [
        'status' => $status,
        'data' => $data,
        'error' => [
            'form' => $errorFrom,
            'message' => $errorMessage
        ],
        'success' => [
            'form' => $successForm,
            'message' => $successMessage
        ]
    ];
    return $res;
}
