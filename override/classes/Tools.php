<?php

class Tools extends ToolsCore {

    public static function displayOgonekPrice($price, $currency = null, $no_utf8 = false, Context $context = null)
    {
        $no_c_char = false;

        if (!is_numeric($price)) {
            return $price;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if ($currency === null) {
            $no_c_char = true;
            $currency = $context->currency;
        } elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance((int)$currency);
        }

        if (is_array($currency)) {
            $c_char = $no_c_char ? '' : $currency['sign'];
            $c_format = $currency['format'];
            $c_decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;
            $c_blank = $currency['blank'];
        } elseif (is_object($currency)) {
            $c_char = $no_c_char ? '' : $currency->sign;
            $c_format = $currency->format;
            $c_decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
            $c_blank = $currency->blank;
        } else {
            return false;
        }

        $blank = ($c_blank ? ' ' : '');
        $ret = 0;
        if (($is_negative = ($price < 0))) {
            $price *= -1;
        }
        $price = Tools::ps_round($price, $c_decimals);

        /*
        * If the language is RTL and the selected currency format contains spaces as thousands separator
        * then the number will be printed in reverse since the space is interpreted as separating words.
        * To avoid this we replace the currency format containing a space with the one containing a comma (,) as thousand
        * separator when the language is RTL.
        *
        * TODO: This is not ideal, a currency format should probably be tied to a language, not to a currency.
        */
        if (($c_format == 2) && ($context->language->is_rtl == 1)) {
            $c_format = 4;
        }

        switch ($c_format) {
            /* X 0,000.00 */
            case 1:
                $ret = $c_char.$blank.number_format($price, $c_decimals, '.', ',');
                break;
            /* 0 000,00 X*/
            case 2:
                $ret = number_format($price, $c_decimals, ',', ' ');//.$blank.$c_char;
                $ret_parts = explode(',', $ret);
                $ret = "{$ret_parts[0]},<em>{$ret_parts[1]}</em>{$blank}{$c_char}";
                break;
            /* X 0.000,00 */
            case 3:
                $ret = $c_char.$blank.number_format($price, $c_decimals, ',', '.');
                break;
            /* 0,000.00 X */
            case 4:
                $ret = number_format($price, $c_decimals, '.', ',').$blank.$c_char;
                break;
            /* X 0'000.00  Added for the switzerland currency */
            case 5:
                $ret = number_format($price, $c_decimals, '.', "'").$blank.$c_char;
                break;
        }
        if ($is_negative) {
            $ret = '-'.$ret;
        }
        if ($no_utf8) {
            return str_replace('€', chr(128), $ret);
        }
        return $ret;
    }

    public static function dieJson($json) {
        header('Content-Type: application/json; charset=UTF-8');
        die(Tools::jsonEncode($json));
    }
}