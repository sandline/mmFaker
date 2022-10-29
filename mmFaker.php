<?php

/**
 * Fake Data Generation Toolkit
 *
 * Copyright 2014 Marco Muracchioli
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * By removing, editing or commenting line 23 (that begin with Die) you
 * accept the licence conditions.
 */
echo "\n\nmmFaker: Using this class you accept the licensing. See mmFaker.php source file and read comments.\n\n";

/**
 * Fake Data Generation Toolkit Class
 *
 * @author Marco Muracchioli <marco.muracchioli@gmail.com>
 * @copyright (C) 2014 Marco Muracchioli
 * @license Apache License 2.0 <http://www.apache.org/licenses/LICENSE-2.0>
 */
class mmFaker
{

    const TYPE_INTEGER          = 1;
    const TYPE_IPADDRESS        = 2;
    const TYPE_DECIMAL          = 3;
    const TYPE_USERNAME         = 4;
    const TYPE_MAIL             = 5;
    const TYPE_TEXT             = 6;
    const TYPE_BITMAP           = 7;
    const TYPE_PASSWORD         = 8;
    const TYPE_CREDITCARD       = 9;
    const TYPE_PERSONNAME       = 10;
    const TYPE_PERSONSURNAME    = 11;
    const TYPE_FULLPERSONNAME   = 12;

    const RANDOM_VALUE    = 1;
    const FIXED_VALUE     = 2;

    const PWD_PLAINTEXT   = 0;
    const PWD_PASSWORD    = 1;
    const PWD_SHA2_256    = 2;
    const PWD_SHA2_384    = 3;
    const PWD_SHA2_512    = 4;

    const CARD_VISA             = 16;
    const CARD_VISA13           = 13;
    const CARD_DINERS           = 14;
    const CARD_MASTERCARD       = 16;
    const CARD_AMERICANEXPRESS  = 15;
    const CARD_JCP              = 16;
    const CARD_DISCOVER         = 16;

    const LISTTYPE_USERNAMES        = 'usernames';
    const LISTTYPE_PERSONNAMES      = 'person_names';
    const LISTTYPE_PERSONSURNAMES   = 'person_surnames';
    const LISTTYPE_TITLES           = 'titles';
    const LISTTYPE_PARAGRAPHS       = 'paragraphs';
    const LISTTYPE_MAILSERVERS      = 'mailservers';

    protected $lists = [
        self::LISTTYPE_USERNAMES      => __DIR__.'/lists/usernames.list',
        self::LISTTYPE_PERSONNAMES    => __DIR__.'/lists/italian_names.list',
        self::LISTTYPE_PERSONSURNAMES => __DIR__.'/lists/italian_surnames.list',
        self::LISTTYPE_TITLES         => __DIR__.'/lists/paragraphs.list',
        self::LISTTYPE_PARAGRAPHS     => __DIR__.'/lists/titles.list',
        self::LISTTYPE_MAILSERVERS    => __DIR__.'/lists/email_servers.list',
    ];

    private $userNames          = null;
    private $textParagraph      = null;
    private $textTitles         = null;
    private $emailServers       = null;
    private $personNames        = null;
    private $personSurnames     = null;

    private $validUserNameChars = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', '.');
    private $validEmailChars    = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '-', '_', '.');

    protected $tableName        = null;
    protected $truncateTable    = null;
    protected $columns          = array();
    protected $values           = array();
    protected $rows             = array();

    protected $totUserNames             = 0;
    protected $totTextParagraph         = 0;
    protected $totTextTitles            = 0;
    protected $totEmailServers          = 0;
    protected $totPersonNames           = 0;
    protected $totPersonSurnames        = 0;
    protected $totValidUserNameChars    = 0;
    protected $totValidEmailChars       = 0;

    /**
     * Class constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->totValidUserNameChars = count($this->validUserNameChars);
        $this->totValidEmailChars = count($this->validEmailChars);
    }

    /**
     * Set a file for specific list contents
     *
     * @param  mixed $listType The list type (one of self::LISTTYPE_*)
     * @param  mixed $filename The filename to use as list source
     * @return bool TRUE if the file exists and the list is set, FALSE otherwise
     */
    public function setList(string $listType, string $filename): bool
    {
        if (file_exists($filename)) {
            $this->lists[$listType] = $filename;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Escape a string for mysql
     *
     * @param string $str The string to escape
     * @return string The escaped string
     * @since 1.0
     * @access protected
     */
    protected function escapeString(string $str): string
    {
        return str_replace(array("\\", "\x00", "\n", "\r", "'", '"', "\x1a"), array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z"), $str);
    }

    /**
     * Set the table name for inserts; you can also specify database.table if you want
     *
     * @param string $name The table name to use
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function setTableName(string $name): mmFaker
    {
        $this->tableName = $name;
        return $this;
    }

    /**
     * Complete reset of the engine (rows, fields, loaded word files)
     *
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function reset(): mmFaker
    {
        unset($this->columns, $this->userNames, $this->textParagraph, $this->textTitles, $this->emailServers);
        $this->columns = array();
        $this->totUserNames = 0;
        $this->totTextParagraph = 0;
        $this->totTextTitles = 0;
        $this->totEmailServers = 0;
        $this->emptyRows();
        return $this;
    }

    /**
     * Add a TRUNCATE TABLE statement before inserts
     *
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function truncate(): mmFaker
    {
        $this->truncateTable = true;
        return $this;
    }

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
    public function addInteger(string $fieldName, int $generationMode = self::RANDOM_VALUE, int $minOrFix = null, int $max = null): mmFaker
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_INTEGER,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == self::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == self::RANDOM_VALUE ? $max : null,
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

    /**
     * Add an IP address to the fields
     *
     * @param  string $fieldName The field name
     * @param int $generationMode mmFaker::FIXED_VALUE or mmFaker::RANDOM_VALUE
     * @param  bool $ipv4 TRUE if the address must be IPv4 (if IPv6 is also true, return a random IP type 4/6)
     * @param  bool $ipv6 TRUE if the address must be IPv6 (if IPv4 is also true, return a random IP type 4/6)
     * @param  string $fixedValue Fixed value for FIXED_VALUE generation mode
     * @return mmFaker The $this class reference for chain-of
     */
    public function addIPAddress(string $fieldName, int $generationMode = self::RANDOM_VALUE, bool $ipv4 = true, bool $ipv6 = false, string $fixedValue = null): mmFaker
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_IPADDRESS,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "ipv4"      => $generationMode == self::RANDOM_VALUE ? $ipv4 : true,
            "ipv6"      => $generationMode == self::RANDOM_VALUE ? $ipv6 : true,
            "fixValue"  => $fixedValue,
        );
        return $this;
    }

    /**
     * Generate an IP address
     *
     * @param  bool $ipv4 TRUE if the address must be IPv4 (if IPv6 is also true, return a random IP type 4/6)
     * @param  bool $ipv6 TRUE if the address must be IPv6 (if IPv4 is also true, return a random IP type 4/6)
     * @return string An IP address
     */
    protected function generateIPAddress($ipv4 = true, $ipv6 = false): string
    {
        switch (true) {
            case ($ipv4 && $ipv6):
                if (rand(0, 1) == 1) {
                    // IPV6
                    return str_replace(array(':::', '::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535))))));
                } else {
                    // IPV4
                    return implode('.', array(rand(0, 220), rand(0, 255), rand(0, 255), rand(0, 254)));
                }
                break;
            case ($ipv4):
                return implode('.', array(rand(0, 220), rand(0, 255), rand(0, 255), rand(0, 254)));
                break;
            case ($ipv6):
                return str_replace(array(':::', '::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535)), dechex(rand(0, 65535))))));
                break;
        } // switch
    }

    /**
     * Add a decimal field to the row template
     *
     * @param string $fieldName The field name
     * @param int $generationMode mmFaker::FIXED_VALUE or mmFaker::RANDOM_VALUE
     * @param int $minOrFix If you're using mmFaker::FIXED_VALUE is the fixed value to use for decimal, else is the minimum decimal value
     * @param int $max Only when using mmFaker::RANDOM_VALUE is the maximum bitmap value
     * @param int $precision Only when using mmFaker::RANDOM_VALUE is the decimal precision (number of decimal digits)
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function addDecimal(string $fieldName, int $generationMode = self::RANDOM_VALUE, int $minOrFix = null, int $max = null, int $precision = null)
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_DECIMAL,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == self::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == self::RANDOM_VALUE ? $max : null,
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

    /**
     * Add a password field
     *
     * @param string $fieldName The field name
     * @param int $generationMode mmFaker::FIXED_VALUE or mmFaker::RANDOM_VALUE
     * @param int|string $minLengthOrFix Minimum password length if RANDOM_VALUE, else the max pwd length
     * @param int $maxLength The max pwd length or ignored if FIXED_VALUE
     * @param int $encodingType The password hash function to use (one of self::PWD_*)
     * @return mmFaker The $this class reference for chain-of
     */
    public function addPassword($fieldName, $generationMode = self::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null, $encodingType = self::PWD_PASSWORD)
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_PASSWORD,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == self::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == self::RANDOM_VALUE ? $maxLength : null,
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

    public function addUserName($fieldName, $generationMode = self::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totUserNames == 0) {
            $this->userNames = explode("\n", file_get_contents($this->lists[self::LISTTYPE_USERNAMES]));
            $this->totUserNames = count($this->userNames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_USERNAME,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == self::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == self::RANDOM_VALUE ? $maxLength : null,
        );
        return $this;
    }

    public function addPersonName($fieldName, $generationMode = self::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonNames == 0) {
            $this->personNames = explode("\n", file_get_contents($this->lists[self::LISTTYPE_PERSONNAMES]));
            $this->totPersonNames = count($this->personNames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_PERSONNAME,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
        );
        return $this;
    }

    public function addPersonSurname($fieldName, $generationMode = self::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonSurnames == 0) {
            $this->personSurnames = explode("\n", file_get_contents($this->lists[self::LISTTYPE_PERSONSURNAMES]));
            $this->totPersonSurnames = count($this->personSurnames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_PERSONSURNAME,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
        );
        return $this;
    }

    public function addFullPersonName($fieldName, $generationMode = self::RANDOM_VALUE, $fixContent = null)
    {
        if ($this->totPersonNames == 0) {
            $this->personNames = explode("\n", file_get_contents($this->lists[self::LISTTYPE_PERSONNAMES]));
            $this->totPersonNames = count($this->personNames) - 1;
        }
        if ($this->totPersonSurnames == 0) {
            $this->personSurnames = explode("\n", file_get_contents($this->lists[self::LISTTYPE_PERSONSURNAMES]));
            $this->totPersonSurnames = count($this->personSurnames) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_FULLPERSONNAME,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $fixContent,
            "minLength" => $generationMode == null,
            "maxLength" => $generationMode == null,
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

    protected function generateFullPersonName(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personNames[rand(0, $this->totPersonNames)] . ' ' . $this->personSurnames[rand(0, $this->totPersonSurnames)];
        }
    }

    protected function generatePersonName(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personNames[rand(0, $this->totPersonNames)];
        }
    }

    protected function generatePersonSurname(string $fixValue = null)
    {
        if (isset($fixValue)) {
            return $fixValue;
        } else {
            return $this->personSurnames[rand(0, $this->totPersonSurnames)];
        }
    }

    public function addMail($fieldName, $generationMode = self::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totEmailServers == 0) {
            $this->emailServers = explode("\n", file_get_contents($this->lists[self::LISTTYPE_MAILSERVERS]));
            $this->totEmailServers = count($this->emailServers) - 1;
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_MAIL,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == self::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == self::RANDOM_VALUE ? $maxLength : null,
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

    public function addText($fieldName, $generationMode = self::RANDOM_VALUE, $minLengthOrFix = null, $maxLength = null)
    {
        if ($this->totTextParagraph == 0) {
            $this->textParagraph = explode("\n", file_get_contents($this->lists[self::LISTTYPE_PARAGRAPHS]));
            $this->totTextParagraph = count($this->textParagraph) - 1;
            echo "Loaded {$this->totTextParagraph} text paragraph.\n";
        }
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_TEXT,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minLengthOrFix,
            "minLength" => $generationMode == self::RANDOM_VALUE ? $minLengthOrFix : null,
            "maxLength" => $generationMode == self::RANDOM_VALUE ? $maxLength : null,
        );
        return $this;
    }

    protected function generateText($minLen = null, $maxLen = null)
    {
        $text = "";
        $done = false;
        while (!$done) {
            $text .= $this->textParagraph[rand(0, $this->totTextParagraph - 1)];
            $textLen = strlen($text);
            if ((isset($minLen) && ($textLen >= $minLen)) && isset($maxLen)) {
                if ($textLen >= $maxLen) {
                    $text = substr($text, 0, strpos($text, ' ', $maxLen));
                    $done = true;
                }
            } elseif (isset($minLen) && ($textLen >= $minLen)) {
                $done = true;
            } elseif (isset($maxLen) && ($textLen <= $maxLen)) {
                $text = substr($text, 0, strpos($text, ' ', $maxLen));
                $done = true;
            } else {
                $done = true;
            }
        }
        return $text;
    }

    /**
     * Add a bitmap field to the row template
     *
     * @param string $fieldName The field name
     * @param int $generationMode mmFaker::FIXED_VALUE or mmFaker::RANDOM_VALUE
     * @param int $minOrFix If you're using mmFaker::FIXED_VALUE is the fixed value to use for bitmap, else is the minimum bitmap value
     * @param int $max Only when using mmFaker::RANDOM_VALUE is the maximum bitmap value
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function addBitMap($fieldName, $generationMode = self::RANDOM_VALUE, $minOrFix = null, $max = null)
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_BITMAP,
            "random"    => $generationMode == self::RANDOM_VALUE ? true : false,
            "fixValue"  => $generationMode == self::RANDOM_VALUE ? null : $minOrFix,
            "min"       => $generationMode == self::RANDOM_VALUE ? $minOrFix : null,
            "max"       => $generationMode == self::RANDOM_VALUE ? $max : null,
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

    public function addCreditCard($fieldName, $cardType)
    {
        $this->columns[$fieldName] = array(
            "type"      => self::TYPE_CREDITCARD,
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

    /**
     * Generate one insert row using user-added rules
     *
     * @return string A single insert row
     * @since 1.0
     * @access protected
     */
    protected function createRow()
    {
        $valList = array();
        foreach ($this->columns as $fieldName => $columnInfo) {
            if ($columnInfo['random']) {
                switch ($columnInfo['type']) {
                    case self::TYPE_INTEGER:
                        $valList[] = $this->escapeString($this->generateInteger($columnInfo['min'], $columnInfo['max']));
                        break;
                    case self::TYPE_IPADDRESS:
                        $valList[] = "'" . $this->escapeString($this->generateIPAddress($columnInfo['ipv4'], $columnInfo['ipv6'])) . "'";
                        break;
                    case self::TYPE_DECIMAL:
                        $valList[] = $this->escapeString($this->generateDecimal($columnInfo['min'], $columnInfo['max'], $columnInfo['precision']));
                        break;
                    case self::TYPE_USERNAME:
                        $valList[] = "'" . $this->escapeString($this->generateUserName($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
                        break;
                    case self::TYPE_MAIL:
                        $valList[] = "'" . $this->escapeString($this->generateMail($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
                        break;
                    case self::TYPE_TEXT:
                        $valList[] = "'" . $this->escapeString($this->generateText($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
                        break;
                    case self::TYPE_BITMAP:
                        $valList[] = "b'" . $this->escapeString($this->generateBitMap($columnInfo['min'], $columnInfo['max'])) . "'";
                        break;
                    case self::TYPE_PASSWORD:
                        switch ($columnInfo['encoding']) {
                            case self::PWD_PASSWORD:
                                $valList[] = "PASSWORD('" . $this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength'])) . "')";
                                break;
                            case self::PWD_SHA2_256:
                                $valList[] = "SHA2('" . $this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength'])) . "', 256)";
                                break;
                            case self::PWD_SHA2_384:
                                $valList[] = "SHA2('" . $this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength'])) . "', 384)";
                                break;
                            case self::PWD_SHA2_512:
                                $valList[] = "SHA2('" . $this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength'])) . "', 512)";
                                break;
                            case self::PWD_PLAINTEXT:
                            default:
                                $valList[] = "'" . $this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
                        }
                        break;
                    case self::TYPE_CREDITCARD:
                        $valList[] = "'" . $this->escapeString($this->generateText($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
                        break;
                    case self::TYPE_PERSONNAME:
                        $valList[] = "'" . $this->escapeString($this->generatePersonName($columnInfo['fixValue'])) . "'";
                        break;
                    case self::TYPE_PERSONSURNAME:
                        $valList[] = "'" . $this->escapeString($this->generatePersonSurname($columnInfo['fixValue'])) . "'";
                        break;
                    case self::TYPE_FULLPERSONNAME:
                        $valList[] = "'" . $this->escapeString($this->generateFullPersonName($columnInfo['fixValue'])) . "'";
                        break;
                } // switch
            } else {
                switch ($columnInfo['type']) {
                    case self::TYPE_INTEGER:
                    case self::TYPE_DECIMAL:
                        $valList[] = $this->escapeString($columnInfo['fixValue']);
                        break;
                    case self::TYPE_IPADDRESS:
                    case self::TYPE_USERNAME:
                    case self::TYPE_MAIL:
                    case self::TYPE_TEXT:
                    case self::TYPE_PERSONNAME:
                        //var_dump($columnInfo['fixValue']);
                        $valList[] = "'" . $this->escapeString($columnInfo['fixValue']) . "'";
                        break;
                    case self::TYPE_BITMAP:
                        $valList[] = "b'" . $this->escapeString(decbin($columnInfo['fixValue'])) . "'";
                        break;
                } // switch
            }
        }
        $this->values[] = $valList;
    }

    /**
     * Generate requested number of rows and store in internal rows array
     *
     * @param string $totalRows The total number of rows to generate
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function createRows($totalRows)
    {
        $from = count($this->values);
        $to = count($this->values) + $totalRows;
        for ($i = $from; $i < $to; $i++) {
            echo ".";
            $this->createRow();
        }
        for ($i = $from; $i < $to - 1; $i++) {
            $this->rows[] = "(" . implode(',', $this->values[$i]) . "),";
        }
        $this->rows[] = "(" . implode(',', $this->values[$to - 1]) . ");";
        echo "\n";
        return $this;
    }

    /**
     * Empty the internal rows array
     *
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function emptyRows()
    {
        unset($this->rows, $this->values);
        $this->rows = array();
        $this->values = array();
        return $this;
    }

    /**
     * Output current rows array to file including truncate (if used) and field list
     *
     * @param string $fileName The file where you want to output the full insert statement
     * @return mmFaker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function toFile($fileName)
    {
        $baseInsert = "";
        if ($this->truncateTable) {
            $baseInsert = "TRUNCATE TABLE {$this->tableName};\n";
        }
        $baseInsert .= "INSERT INTO {$this->tableName}\n(";
        foreach ($this->columns as $fieldName => $columnInfo) {
            $baseInsert .= "{$fieldName},";
        }
        $baseInsert = substr($baseInsert, 0, -1) . ")\nVALUES\n";
        file_put_contents($fileName, $baseInsert . implode("\n", $this->rows));
        return $this;
    }
}
