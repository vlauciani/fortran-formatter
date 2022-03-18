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
                } else if ($explode__format_value[1] == 0) {
                    /* if '$value=22.995' formatted by 'F5.2' this variable will be 0.99 */
                    $fractional_rounded_by_format_value = round($value - intval($value), 1);

                    /* if '$format=F3.0' it will be '3' */
                    $f = $explode__format_value[0];

                    if (intval($value) == 0) {
                        $r = ($f - 1);
                    } else {
                        /**
                         * Faccio il round() del $value arrotondato a '3' (deriva da F3.0) meno il numero di interi del $value
                         *  poi sottraggo ancora '-1' che e' l'occupazione in caratteri del (punto) '.'.
                         * Quindi in caso di '$value=19.285', $k e' uguale a round(19.285, (2 - 1));
                         *  Con:
                         *   '2': il numero di interi di $value, '19'
                         *   '1': il punto considerato come carattere
                         * Il risultato e' un float: '19.0'
                         */
                        $r = ($f - strlen(intval($value)) - 1);
                    }
                    if ($r < 0) {
                        $r = 0;
                    }
                    $k = round($value, $r);

                    /* Cerco se all'interno di $k c'e' il punto; questo perche un float 19.0 convertito in stringa diventa 19 */
                    $kk = strval($k);
                    if (!strpos($kk, '.')) {
                        $kk .= '.';
                    }

                    /* Passo da '0.12' a '.12' */
                    if (intval($value) == 0) {
                        $kk = substr($kk, 1);
                    }

                    /*  Il numero arrotondato e' maggiore del numero richeistp; se ho 999.9123 a F3.0, non posso scrivere 1000 e quindi errore */
                    if (strlen($k) > strlen(intval($value))) {
                        return str_pad(
                            '',
                            $f,
                            '*',
                            STR_PAD_RIGHT
                        );
                    }

                    /* Cmq ritorno solo un valore come $f; quindi se F3.0 ritorno solo 3 caratteri */
                    return substr($kk, 0, $f);
                }

                dd($p, $fractional_rounded_by_format_value, $value, $return, $explode__format_value);
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
                            //echo "exit=1<br>";
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

                /* To write test case */
                /*
                if ($value != 0) {
                    if ($float_format == 'old') {
                        $valueToLog = $valueNew;
                        $valueToLogExtra = ', STR_PAD_LEFT, \'old\'';
                    } else {
                        $valueToLog = $value;
                        $valueToLogExtra = '';
                    }
                    \Log::debug('|$this->assertEquals(\'' . $return . '\', self::fromFortranFormatToString(\'' . $format . '\', \'' . $valueToLog . '\', $str_pad_string' . $valueToLogExtra . '));|');
                }
                */
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
