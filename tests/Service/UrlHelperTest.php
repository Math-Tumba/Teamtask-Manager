<?php

namespace App\Tests\Service;

use App\Service\UrlHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

#[CoversClass(UrlHelper::class)]
class UrlHelperTest extends TestCase
{
    private UrlHelper $urlHelper;

    protected function setUp(): void
    {
        $this->urlHelper = new UrlHelper();
    }



    #[DataProvider('stringUrlProvider')]
    public function testGetDomainName(?string $input, ?string $expected): void
    {
        $res = $this->urlHelper->getDomainName($input);

        assertEquals($expected, $res);

        return;
    }
    public static function stringUrlProvider() : array {
        return [
            'Null' => [null, null],
            'String' => ['SimpleString', null],
            'Unvalid url' => ['http:/test.com', null],
            'Valid url with scheme and host only' => ['http://test.com', 'test.com'],
            'Valud url with subdomain' => ['http://www.test.com', 'www.test.com'],
            'Valid url with page' => ['http://www.test.com/page.php', 'www.test.com'],
            'Valid url with multiple subdomains' => ['http://www.subtest.test.com/page.php', 'www.subtest.test.com'],
            'Valid url with user and query' => ['http://username:password@www.test.com?query=test1234', 'www.test.com'],
        ];
    }
}
