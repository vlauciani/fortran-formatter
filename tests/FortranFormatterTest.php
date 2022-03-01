<?php

namespace VLauciani\FortranFormatter\Tests;

use Orchestra\Testbench\TestCase;
use VLauciani\FortranFormatter\Traits\FortranFormatTrait;


class FortranFormatterTestTest extends TestCase
{
    use FortranFormatTrait;

    public function test_trait()
    {
        $str_pad_string = '*';

        // new
        $this->assertEquals('ACER*', self::fromFortranFormatToString('A5', 'ACER', $str_pad_string, STR_PAD_RIGHT));
        $this->assertEquals('IV', self::fromFortranFormatToString('A2', 'IV', $str_pad_string));
        $this->assertEquals('*12', self::fromFortranFormatToString('F3.0', '12', $str_pad_string));
        $this->assertEquals('******', self::fromFortranFormatToString('6X', null, $str_pad_string));
        $this->assertEquals('***123', self::fromFortranFormatToString('I6', '123', $str_pad_string));
        // new - float
        $this->assertEquals('***01', self::fromFortranFormatToString('F5.2', '1', $str_pad_string));
        $this->assertEquals('***12', self::fromFortranFormatToString('F5.2', '12', $str_pad_string));
        $this->assertEquals('**123', self::fromFortranFormatToString('F5.2', '123', $str_pad_string));
        $this->assertEquals('*1234', self::fromFortranFormatToString('F5.2', '1234', $str_pad_string));
        $this->assertEquals('12345', self::fromFortranFormatToString('F5.2', '12345', $str_pad_string));

        // old - float
        $this->assertEquals('*0.01', self::fromFortranFormatToString('F5.2', '1', $str_pad_string, STR_PAD_LEFT, 'old'));
        $this->assertEquals('*0.12', self::fromFortranFormatToString('F5.2', '12', $str_pad_string, STR_PAD_LEFT, 'old'));
        $this->assertEquals('*1.23', self::fromFortranFormatToString('F5.2', '123', $str_pad_string, STR_PAD_LEFT, 'old'));
        $this->assertEquals('12.34', self::fromFortranFormatToString('F5.2', '1234', $str_pad_string, STR_PAD_LEFT, 'old'));
        $this->assertEquals('123.5', self::fromFortranFormatToString('F5.2', '12345', $str_pad_string, STR_PAD_LEFT, 'old'));
    }
}
