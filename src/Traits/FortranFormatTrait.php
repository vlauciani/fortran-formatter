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
    public static function fromFortranFormatToString($format, $value, $str_pad_string, $str_pad_type = STR_PAD_LEFT)
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
}
