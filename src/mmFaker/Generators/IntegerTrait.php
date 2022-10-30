<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait IntegerTrait
{

    /**
     * Add an integer field to the row template
     *
     * @param string $fieldName The field name
     * @param int $generationMode mmFaker::FIXED_VALUE or mmFaker::RANDOM_VALUE
     * @param int $minOrFix If you're using mmFaker::FIXED_VALUE is the fixed value to use for integer, else is the minimum integer value
     * @param int $max Only when using mmFaker::RANDOM_VALUE is the maximum integer value
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function addInteger(string $fieldName, int $generationMode = Faker::RANDOM_VALUE, int $minOrFix = null, int $max = null): Faker
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_INTEGER,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == Faker::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == Faker::RANDOM_VALUE ? $max : null,
        );
        return $this;
    }

    /**
     * Generate a random integer number
     *
     * @param int|float $min The minimum range for the number
     * @param int|float $max The maximum range for the number
     * @return int The random number
     * @since 1.0
     * @access protected
     * @see addInteger
     */
    protected function generateInteger(int|float $min, int|float $max): int|float
    {
        return rand($min, $max);
    }
}
