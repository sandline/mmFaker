<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait DecimalTrait
{
    /**
     * Add a decimal field to the row template
     *
     * @param string $fieldName The field name
     * @param int $generationMode Faker::FIXED_VALUE or Faker::RANDOM_VALUE
     * @param int $minOrFix If you're using Faker::FIXED_VALUE is the fixed value to use for decimal, else is the minimum decimal value
     * @param int $max Only when using Faker::RANDOM_VALUE is the maximum bitmap value
     * @param int $precision Only when using Faker::RANDOM_VALUE is the decimal precision (number of decimal digits)
     * @return Faker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function addDecimal(string $fieldName, int $generationMode = Faker::RANDOM_VALUE, int $minOrFix = null, int $max = null, int $precision = null)
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_DECIMAL,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == Faker::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == Faker::RANDOM_VALUE ? $max : null,
            "precision" => $precision,
        );
        return $this;
    }

    /**
     * Generate a random decimal number
     *
     * @return float The decimal number
     * @since 1.0
     * @access protected
     * @see addDecimal
     */
    protected function generateDecimal($min, $max, $precision)
    {
        $num = (float)rand() / (float)getrandmax();
        return round($num * ($max - $min) + $min, $precision);
    }
}
