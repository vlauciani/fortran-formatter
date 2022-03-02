<?php

namespace VLauciani\FortranFormatter\Traits;

trait FortranFormatTrait
{

    /*
     * Get '$value' and Fortran '$format' and return formatted string
     * 
     * @param type $format:          Fortran format (es. F5.2, I4, A3)
     * @param type $value:           The value to convert to string by Fortran format
     * @param type $str_pad_string:  The char used to pad the string. More info here: https://secure.php.net/manual/en/function.str-pad.php
     * @param type $str_pad_type:    Where to add the '$str_pad_string' Mode info here: https://secure.php.net/manual/en/function.str-pad.php
     * @return String
     */
    public static function fromFortranFormatToStringOld($format, $value, $str_pad_string, $str_pad_type = STR_PAD_LEFT)
    {
        if (substr($format, -1) == 'X') {
            $format_type  = 'X';
            $format_value = str_replace('X', '', $format);
        } else {
            // Get first '$format' char; should be 'I' (for Integer), 'F' (for Float), ecc...
            $format_type  = strtoupper(substr($format, 0, 1));
            // Get from second to the end '$format' value; could be '2', '4.2', ecc...
            $format_value = substr($format, 1);
        }

        switch ($format_type) {
            case 'I':
                return str_pad($value, $format_value, $str_pad_string, $str_pad_type);
                break;
            case 'F':
                $explode__format_value = explode('.', $format_value);

                if (is_null($value)) {
                    $value = 0;
                }

                // Get the whole part of $value
                if (intval($value) == 0) {
                    $whole = str_pad(
                        '',
                        ($explode__format_value[0] - $explode__format_value[1]),
                        $str_pad_string,
                        $str_pad_type
                    );
                } else {
                    $whole = str_pad(
                        intval($value),
                        ($explode__format_value[0] - $explode__format_value[1]),
                        $str_pad_string,
                        $str_pad_type
                    );
                }
                $return = $whole;

                // Get the fractional part of $value
                if ($explode__format_value[1] > 0) {
                    /* if '$value=22.995' formatted by 'F5.2' this variable will be 0.99 */
                    $fractional_rounded_by_format_value = round($value - intval($value), $explode__format_value[1]);

                    $fraction = str_pad(
                        explode(
                            '.',
                            number_format(
                                $fractional_rounded_by_format_value,
                                $explode__format_value[1],
                                '.',
                                ''
                            )
                        )[1],
                        $explode__format_value[1],
                        $str_pad_string,
                        $str_pad_type
                    );

                    /**
                     * Con questo IF controllo se il numeri di 'zeri' nella variable/stringa '$fraction' e' uguale all'arrotondamento nel 'format'.
                     *  ad esempio se `$value=22.000` e format e' `F5.2`, la frazione conterrà '00' che sono due caratteri (come il '2' di 'F5.2')
                     *  e quindi faccio il `str_pad()` ed elimino gli `00`.
                     */
                    if (substr_count($fraction, '0') == $explode__format_value[1]) {
                        $fraction = str_pad('', $explode__format_value[1], $str_pad_string, $str_pad_type);
                        /**
                         * Se il round della frazione `$fractional_rounded_by_format_value` ritorna un 'intero', ad esempio 
                         *  quando ho '$value=22.996' formattato a 'F5.2' la frazione 0.996 con round(2) è '1.00',
                         *  allora devo aggiungere l'intero alla variabile '$whole/$return'
                         */
                        if (intval($fractional_rounded_by_format_value) == 1) {
                            $return = str_pad(
                                intval($value + 1),
                                ($explode__format_value[0] - $explode__format_value[1]),
                                $str_pad_string,
                                $str_pad_type
                            );
                        }
                    }
                    $return .= $fraction;
                }

                // Check that result doesn't contain only '0'
                if (empty(str_replace("0", '', $return))) {
                    $return = str_replace("0", $str_pad_string, $return);
                }

                // Set sign
                if ($value < 0) {
                    $return = strrev(preg_replace(strrev("/=/"), strrev('-'), strrev($return), 1));
                }

                return $return;
                break;
            case 'A':
                return str_pad($value, $format_value, $str_pad_string, $str_pad_type);
                break;
            case 'X':
                return str_pad('', $format_value, $str_pad_string, $str_pad_type);
                break;
        }
    }

    /*
     * Get '$value' and Fortran '$format' and return formatted string
     * 
     * @param type $format:          Fortran format (es. F5.2, I4, A3)
     * @param type $value:           The value to convert to string by Fortran format
     * @param type $str_pad_string:  The char used to pad the string. More info here: https://secure.php.net/manual/en/function.str-pad.php
     * @param type $str_pad_type:    Where to add the '$str_pad_string' Mode info here: https://secure.php.net/manual/en/function.str-pad.php
     * @param type $float_format     Could be 'new' or 'old'
     *  1)'new': doesn't print point. Es: 12.34 formatted with F5.2 will be ' 1234'
     *  2)'old': print point. Es: 12.34 formatted with F5.2 will be '12.34'
     * @return String
     */
    public static function fromFortranFormatToString($format, $value, $str_pad_string, $str_pad_type = STR_PAD_LEFT, $float_format = 'new')
    {
        /* Check $float_format */
        if ($float_format != 'old' && $float_format != 'new') {
            $float_format = 'new';
        }

        if (substr($format, -1) == 'X') {
            $format_type  = 'X';
            $format_value = str_replace('X', '', $format);
        } else {
            // Get first '$format' char; should be 'I' (for Integer), 'F' (for Float), ecc...
            $format_type  = strtoupper(substr($format, 0, 1));
            // Get from second to the end '$format' value; could be '2', '4.2', ecc...
            $format_value = substr($format, 1);
        }

        switch ($format_type) {
            case 'I':
                return str_pad($value, $format_value, $str_pad_string, $str_pad_type);
                break;
            case 'F':
                $explode__format_value = explode(
                    '.',
                    $format_value
                );

                if (!str_contains($value, '.')) {
                    (float)$value = $value / pow(10, $explode__format_value[1]);
                }

                if (is_null($value)) {
                    $value = 0;
                }

                // Get the whole part of $value
                if (intval($value) == 0) {
                    $whole = str_pad(
                        '',
                        ($explode__format_value[0] - $explode__format_value[1]),
                        $str_pad_string,
                        $str_pad_type
                    );
                } else {
                    $whole = str_pad(
                        intval($value),
                        ($explode__format_value[0] - $explode__format_value[1]),
                        $str_pad_string,
                        $str_pad_type
                    );
                }
                $return = $whole;

                // Get the fractional part of $value
                if ($explode__format_value[1] > 0) {
                    /* if '$value=22.995' formatted by 'F5.2' this variable will be 0.99 */
                    $fractional_rounded_by_format_value = round($value - intval($value), $explode__format_value[1]);

                    $fraction = str_pad(
                        explode(
                            '.',
                            number_format(
                                $fractional_rounded_by_format_value,
                                $explode__format_value[1],
                                '.',
                                ''
                            )
                        )[1],
                        $explode__format_value[1],
                        $str_pad_string,
                        $str_pad_type
                    );

                    /**
                     * Con questo IF controllo se il numeri di 'zeri' nella variable/stringa '$fraction' e' uguale all'arrotondamento nel 'format'.
                     *  ad esempio se `$value=22.000` e format e' `F5.2`, la frazione conterrà '00' che sono due caratteri (come il '2' di 'F5.2')
                     *  e quindi faccio il `str_pad()` ed elimino gli `00`.
                     */
                    if (substr_count($fraction, '0') == $explode__format_value[1]) {
                        $fraction = str_pad('', $explode__format_value[1], $str_pad_string, $str_pad_type);
                        /**
                         * Se il round della frazione `$fractional_rounded_by_format_value` ritorna un 'intero', ad esempio 
                         *  quando ho '$value=22.996' formattato a 'F5.2' la frazione 0.996 con round(2) è '1.00',
                         *  allora devo aggiungere l'intero alla variabile '$whole/$return'
                         */
                        if (intval($fractional_rounded_by_format_value) == 1) {
                            $return = str_pad(
                                intval($value + 1),
                                ($explode__format_value[0] - $explode__format_value[1]),
                                $str_pad_string,
                                $str_pad_type
                            );
                        }
                    }
                    $return .= $fraction;
                }

                // Check that result doesn't contain only '0'
                if (empty(str_replace("0", '', $return))) {
                    $return = str_replace("0", $str_pad_string, $return);
                }

                // Set sign
                if ($value < 0) {
                    $return = strrev(preg_replace(strrev("/=/"), strrev('-'), strrev($return), 1));
                }

                if ($float_format == 'old') {
                    $c = $explode__format_value[1];
                    $valueNew = number_format($value, $c, '.', '');
                    //echo "start__value:" . $valueNew . "<br>";
                    //echo "start__c:" . $c . "<br>";
                    //echo "explode__format_value[0]:" . $explode__format_value[0] . "<br><br>";
                    $exit = 0;
                    while (strlen($valueNew) > $explode__format_value[0] && $exit == 0) {
                        //echo "strln(value):" . strlen($valueNew) . "<br>";
                        //echo "c:" . $c . "<br>";
                        if ($c == 0) {
                            echo "exit=1<br>";
                            $exit = 1;
                        } else {
                            $c = ($c - 1);
                            $valueNew = number_format($valueNew, $c, '.', '');
                            //echo "value:" . $valueNew . "<br><br>";
                        }
                    }
                    //echo "value_just_exit:" . $valueNew . "<br><br>";
                    if ($exit == 1 || $c == 0) {
                        //echo "A<br>";
                        $return = number_format($valueNew, 0, '.', '') . '.';
                        if (strlen($return) > $explode__format_value[0]) {
                            $return = str_pad('', $explode__format_value[0], '*', $str_pad_type);
                        }
                    } else {
                        //echo "B<br>";
                        $a = number_format($valueNew, $c, '.', '');
                        $return = str_pad($a, $explode__format_value[0], $str_pad_string, $str_pad_type);
                        //echo "B1<br>";
                    }
                }
                //\Log::debug("TRAIT - " . __CLASS__ . ' -> ' . __FUNCTION__ . ' -> $format=' . $format . ', $value=' . $value . ', $return(len=' . strlen($return) . ')=|' . $return . '|');
                //if ($value != 0) {
                //    \Log::debug('|$this->assertEquals(\'' . $return . '\', self::fromFortranFormatToString(\'' . $format . '\', \'' . $value . '\', $str_pad_string));|');
                //}
                return $return;
                break;
            case 'A':
                return str_pad($value, $format_value, $str_pad_string, $str_pad_type);
                break;
            case 'X':
                return str_pad('', $format_value, $str_pad_string, $str_pad_type);
                break;
        }
    }
}
