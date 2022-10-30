<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait UsernameTrait
{
    public function addUserName($fieldName, $generationMode = Faker::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totUserNames == 0) {
            $this->userNames = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_USERNAMES]));
            $this->totUserNames = count($this->userNames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_USERNAME,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == Faker::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == Faker::RANDOM_VALUE ? $maxLength : null,
        );
        return $this;
    }

    protected function generateUserName($minLen = null, $maxLen = null)
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
        $itemId = rand(1, $this->totUserNames) - 1;
        while (!$checkFunc(strlen($this->userNames[$itemId]), $minLen, $maxLen)) {
            $itemId = rand(1, $this->totUserNames) - 1;
        }
        return $this->userNames[$itemId];
    }
}
