<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiHelper {

    public function getErrorMessage(ResponseInterface $response, string $defaultMessage = "Une erreur est survenue."): string {
        try {
            $data = json_decode($response->getContent(false), true);
            return $data['message'] ?? $defaultMessage;
        } catch (\Exception $e) {
            return $defaultMessage;
        }
    }
}