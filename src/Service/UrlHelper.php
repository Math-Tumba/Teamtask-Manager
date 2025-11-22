<?php

namespace App\Service;

final class UrlHelper
{
    /**
     * Gets the host name of an URL.
     *
     * Allowing null value since parse_url(null, ?) is deprecated.
     *
     * @return string
     */
    public function getDomainName(?string $url)
    {
        if (null === $url) {
            return null;
        }

        return parse_url($url, PHP_URL_HOST);
    }
}
