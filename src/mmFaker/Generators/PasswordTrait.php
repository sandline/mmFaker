<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait PasswordTrait
{
    /**
     * Add a password field
     *
     * @param string $fieldName The field name
     * @param int $generationMode Faker::FIXED_VALUE or Faker::RANDOM_VALUE
     * @param int|string $minLengthOrFix Minimum password length if RANDOM_VALUE, else the max pwd length
     * @param int $maxLength The max pwd length or ignored if FIXED_VALUE
     * @param int $encodingType The password hash function to use (one of Faker::PWD_*)
     * @return Faker The $this class reference for chain-of
     */
    public function addPassword($fieldName, $generationMode = Faker::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null, $encodingType = Faker::PWD_PASSWORD)
    {
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_PASSWORD,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == Faker::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == Faker::RANDOM_VALUE ? $maxLength : null,
            "encoding"  => $encodingType,
        );
        return $this;
    }

    protected function generatePassword($minLen = null, $maxLen = null)
    {
        switch (true) {
            case (isset($minLen) && isset($maxLen)):
                $checkFunc = function ($len, $min, $max) {
                    return ($len >= $min) && ($len <= $max);
                };
                break;
            case (isset($minLen)):
                $checkFunc = function ($len, $min, $max) {
                    return $len >= $min;
                };
                break;
            case (isset($maxLen)):
                $checkFunc = function ($len, $min, $max) {
                    return $len <= $max;
                };
                break;
            default:
                $checkFunc = function ($len, $min, $max) {
                    return true;
                };
        } // switch
        $pwd = "";
        $upwd = rand($minLen, $maxLen);
        for ($i = 0; $i < $upwd; $i++) {
            $pwd .= $this->validUserNameChars[rand(0, $this->totValidUserNameChars - 1)];
        }
        return $pwd;
    }
}
