<?php

namespace App\Tests\Service;

use App\Service\ArrayHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(ArrayHelper::class)]
class ArrayHelperTest extends TestCase
{
    private ArrayHelper $arrayHelper;

    protected function setUp(): void
    {
        $this->arrayHelper = new ArrayHelper();

        return;
    }



    #[DataProvider('arrayValuesProvider')]
    public function testAllValuesAreNull(array $input, bool $expected): void
    {
        $res = $this->arrayHelper->allValuesAreNull($input);

        $this->assertSame($expected, $res);

        return;
    }
    public static function arrayValuesProvider(): array
    {
        return [
            'Empty' => [[], true],
            'Null' => [[null], true],
            'Integer' => [[1], false],
            'Boolean' => [[false], false],
            'Empty string' => [[''], false],
            'Multiple empty arrays' => [[[], []], true],
            'Multiple arrays containing values' => [[[1], [], ['rogrkdp']], false],
            'Nested arrays with empty or null values' => [[[null], [[], []], [[null], []]], true],
            'Nested arrays containing not null values' => [[[null], [[50], []], [[null], []]], false],
        ];
    }
}
