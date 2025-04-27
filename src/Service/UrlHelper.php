<?php

namespace App\Service;

class UrlHelper {

    /**
     * Gets the host name of an URL.
     * 
     * Allowing null value since parse_url(null, ?) is deprecated.
     * @param ?string $url
     * 
     * @return string
     */
    public function getDomainName(?string $url) {
        if ($url === null) {
            return null;
        }
        return parse_url($url, PHP_URL_HOST);
    }
} 