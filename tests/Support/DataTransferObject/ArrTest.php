<?php

declare(strict_types=1);

namespace Tests\Support\DataTransferObject;

use Carbon\CarbonImmutable;
use Pingen\Endpoints\DataTransferObjects\Letter\LetterAttributes;
use Pingen\Support\DataTransferObject\DataTransferObject;
use Tests\TestCase;

class ArrTest extends TestCase
{
    public function testPositive(): void
    {
        $expectedArray = [
            'status' => 'sent',
            'file_original_name' => 'lorem.pdf',
            'file_pages' => 2,
            'address_position' => 'left',
            'address' => 'Hans Meier\nExample street 4\n8000 Zürich\nSwitzerland',
            'country' => 'CH',
            'price_currency' => 'CHF',
            'price_value' => 1.25,
            'delivery_product' => 'fast',
            'print_mode' => 'simplex',
            'print_spectrum' => 'color',
            'paper_types' => ['normal', 'qr'],
            'fonts' => [(object)[
                'name' => 'Helvetica',
                'is_embedded' => true
            ]],
            'source' => 'API',
            'tracking_number' => '98.1234.11',
            'submitted_at' => CarbonImmutable::make('2020-11-19T09:42:48+0100'),
            'created_at' => CarbonImmutable::make('2020-11-19T09:42:48+0100'),
            'updated_at' => CarbonImmutable::make('2020-11-19T09:42:48+0100')
        ];

        $letterDTO = new LetterAttributes($expectedArray);

        foreach ($letterDTO->toArray() as $key => $value) {
            $this->assertEquals($expectedArray[$key], $value);
        }

        $this->assertsame(['status' => 'sent'], $letterDTO->only('status')->toArray());
        $this->assertSame([
            'status' => 'sent',
            'price_currency' => 'CHF',
            'price_value' => 1.25,
            'delivery_product' => 'fast',
            'print_mode' => 'simplex',
            'print_spectrum' => 'color',
            'paper_types' => ['normal', 'qr'],
            'source' => 'API',
            'tracking_number' => '98.1234.11',
        ], $letterDTO->except('file_original_name', 'fonts', 'file_pages', 'address_position', 'address', 'country', 'submitted_at', 'created_at', 'updated_at')->toArray());
    }

    public function testForget(): void
    {
        $dto = new SimpleDTO(['status' => 'sent', 'name' => 'example']);
        $this->assertSame(['status' => 'sent', 'name' => 'example'], $dto->except('example.test')->toArray());
    }
}

class SimpleDTO extends DataTransferObject
{
    public string $status;
    public string $name;
}
