<?php

namespace mmFaker\Generators;

use mmFaker\Faker;

trait MailTrait
{
    public function addMail($fieldName, $generationMode = Faker::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totEmailServers == 0) {
            $this->emailServers = explode("\n", file_get_contents($this->lists[Faker::LISTTYPE_MAILSERVERS]));
            $this->totEmailServers = count($this->emailServers) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => Faker::TYPE_MAIL,
            "random"    => $generationMode == Faker::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == Faker::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == Faker::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == Faker::RANDOM_VALUE ? $maxLength : null,
        );
        return $this;
    }

    protected function generateMail($minLen, $maxLen)
    {
        $serverID = rand(1, $this->totEmailServers) - 1;
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
        $uname = "";
        $unameLen = rand(5, $maxLen - strlen($this->emailServers[$serverID]) - 5);
        $unameLen = $unameLen < 5 ? 5 : $unameLen;
        for ($i = 0; $i < $unameLen; $i++) {
            $uname .= $this->validEmailChars[rand(0, $this->totValidEmailChars - 1)];
        }
        return str_replace('..', '.', $uname . $this->emailServers[$serverID]);
    }
}
