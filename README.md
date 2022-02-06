# fortran-formatter
[![Tests](https://github.com/vlauciani/fortran-formatter/actions/workflows/phpunit.yml/badge.svg)](https://github.com/vlauciani/fortran-formatter/actions)
[![Packagist License](https://poser.pugx.org/vlauciani/fortran-formatter/license.png)](http://choosealicense.com/licenses/mit/)
[![Total Downloads](https://poser.pugx.org/vlauciani/fortran-formatter/d/total.png)](https://packagist.org/packages/vlauciani/fortran-formatter)

## Installation
```
composer require vlauciani/fortran-formatter:^1.0.0
```

## Usage
```
<?php
namespace App\Api\Controllers;
use App\Http\Controllers\Controller;
use VLauciani\FortranFormatter\Traits\FortranFormatTrait;

class MyController extends Controller
{
    use FortranFormatTrait;
    
    public function convertData()
    {
	      $str_pad_string = '*';
	      $example_1 = self::fromFortranFormatToString('A5', 'ACER', $str_pad_string, STR_PAD_RIGHT); // return: 'ACER*'
	      $example_2 = self::fromFortranFormatToString('A2', 'IV', $str_pad_string); // return: IV
	      $example_3 = self::fromFortranFormatToString('F3.0', '12', $str_pad_string); // return: *12
	      $example_4 = self::fromFortranFormatToString('6X', null, $str_pad_string); // return: ******
	      $example_5 = self::fromFortranFormatToString('I6', '123', $str_pad_string); // return: ***123
    }
}
```

## Contribute
Thanks to your contributions!

Here is a list of users who already contributed to this repository:
<a href="https://github.com/vlauciani/fortran-formatter/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=vlauciani/fortran-formatter" />
</a>

## Author
(c) 2022 Valentino Lauciani vlauciani[at]gmail.com

