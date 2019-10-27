<?php 

class Format {
    function price_format($price, $currency = '€', $replace_cents = false) {
        $price_money = substr($price, 0, -2);
        $price_cents = substr($price, -2);

        $price = number_format((int)$price, 0, ',', '.');
        $price = $price_money . ',' . $price_cents;

        if($replace_cents === true && $price_cents == '00') {
            $price = substr($price, 0, -2);
            $price = $price + '-';
        }

        return $currency . $price;
    }

    function price_int_format($price, $currency = '€') {
        $price = replace(',', '', $price);
        $price = replace('.', '', $price);
        return $price;
    }
}

?>