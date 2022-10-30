<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait CreditCardTrait
{
    public function addCreditCard($fieldName, $cardType)
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_CREDITCARD,
            "random"    => false,
            "numberLen" => $cardType,
        );
        return $this;
    }

    protected function generateCreditCard($cardType)
    {
        $ccn = "";
        $cca = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9');
        $v = 0;
        for ($i = 0; $i < $cardType - 1; $i++) {
            $c = $cca[rand(0, 9)];
            echo "char " . ($cardType - $i) . " is {$c}\n";
            $n = (int)$c;
            if ($i & 1) {
                $v += $n;
            } else {
                $v += ($n * 2 > 9) ? $n * 2 - 9 : $n * 2;
            }
            $ccn = $c . $ccn;
        }
        $ccn .= 10 - $v % 10;
        return $ccn;
    }
}
