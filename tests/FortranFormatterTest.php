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

	$this->assertEquals('ACER*', self::fromFortranFormatToString('A5', 'ACER', $str_pad_string, STR_PAD_RIGHT));
	$this->assertEquals('IV', self::fromFortranFormatToString('A2', 'IV', $str_pad_string));
	$this->assertEquals('*12', self::fromFortranFormatToString('F3.0', '12', $str_pad_string));
	$this->assertEquals('******', self::fromFortranFormatToString('6X', null, $str_pad_string));
	$this->assertEquals('***123', self::fromFortranFormatToString('I6', '123', $str_pad_string));
    }
}
