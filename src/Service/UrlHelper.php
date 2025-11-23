<?php

namespace App\Service;

final class UrlHelper
{
    /**
     * Gets the host name of an URL.
     *
     * Allowing null value since parse_url(null, ?) is deprecated.
     */
    public function getDomainName(?string $url): ?string
    {
        if (null === $url) {
            return null;
        }

        return parse_url($url, PHP_URL_HOST);
    }
}
