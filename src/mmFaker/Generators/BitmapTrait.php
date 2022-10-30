<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait BitmapTrait
{
    /**
     * Add a bitmap field to the row template
     *
     * @param string $fieldName The field name
     * @param int $generationMode Faker::FIXED_VALUE or Faker::RANDOM_VALUE
     * @param int $minOrFix If you're using Faker::FIXED_VALUE is the fixed value to use for bitmap, else is the minimum bitmap value
     * @param int $max Only when using Faker::RANDOM_VALUE is the maximum bitmap value
     * @return Faker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function addBitMap($fieldName, $generationMode = Faker::RANDOM_VALUE, $minOrFix = null, $max = null)
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_BITMAP,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == Faker::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == Faker::RANDOM_VALUE ? $max : null,
        );
        return $this;
    }

    /**
     * Generate a bitmap
     *
     * @see addBitMap
     * @return float The bitmap
     * @since 1.0
     * @access protected
     */
    protected function generateBitMap($min, $max)
    {
        $maxStr = strlen(decbin($max));
        $realLen = (round($maxStr) % 8 === 0) ? round($maxStr) : round(($maxStr + 8 / 2) / 8) * 8;
        return sprintf("%0" . $realLen . "d", decbin(rand($min, $max)));
    }
}
