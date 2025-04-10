<?php

namespace App\Service;

class UrlHelper {

    public function getDomainName(?string $url) {
        if ($url === null) {
            return null;
        }
        return parse_url($url, PHP_URL_HOST);
    }
} 