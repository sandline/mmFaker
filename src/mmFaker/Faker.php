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
 */

namespace mmFaker;

use mmFaker\Generators\BitmapTrait;
use mmFaker\Generators\CreditCardTrait;
use mmFaker\Generators\DecimalTrait;
use mmFaker\Generators\IntegerTrait;
use mmFaker\Generators\IPAddressTrait;
use mmFaker\Generators\MailTrait;
use mmFaker\Generators\PasswordTrait;
use mmFaker\Generators\PersonTrait;
use mmFaker\Generators\TextParagraphTrait;
use mmFaker\Generators\UsernameTrait;

/**
 * Fake Data Generation Toolkit Class
 *
 * @author Marco Muracchioli <marco.muracchioli@gmail.com>
 * @copyright (C) 2014 Marco Muracchioli
 * @license Apache License 2.0 <http://www.apache.org/licenses/LICENSE-2.0>
 */
class Faker
{
    use BitmapTrait,
        CreditCardTrait,
        DecimalTrait,
        IntegerTrait,
        IPAddressTrait,
        MailTrait,
        PasswordTrait,
        PersonTrait,
        TextParagraphTrait,
        UsernameTrait;

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
        self::LISTTYPE_USERNAMES      => __DIR__.'/../lists/usernames.list',
        self::LISTTYPE_PERSONNAMES    => __DIR__.'/../lists/italian_names.list',
        self::LISTTYPE_PERSONSURNAMES => __DIR__.'/../lists/italian_surnames.list',
        self::LISTTYPE_TITLES         => __DIR__.'/../lists/paragraphs.list',
        self::LISTTYPE_PARAGRAPHS     => __DIR__.'/../lists/titles.list',
        self::LISTTYPE_MAILSERVERS    => __DIR__.'/../lists/email_servers.list',
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
     * @throw \Exception If list file does not exists
     * @return Faker The $this class reference for chain-of
     */
    public function setList(string $listType, string $filename): Faker
    {
        if (file_exists($filename)) {
            $this->lists[$listType] = $filename;
            return $this;
        } else {
            throw new \Exception("List file `{$filename}` not found.");
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
     * @return Faker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function setTableName(string $name): Faker
    {
        $this->tableName = $name;
        return $this;
    }

    /**
     * Complete reset of the engine (rows, fields, loaded word files)
     *
     * @return Faker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function reset(): Faker
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
     * @return Faker The $this class reference for chain-of
     * @since 1.0
     * @access public
     */
    public function truncate(): Faker
    {
        $this->truncateTable = true;
        return $this;
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
                        $valList[] = "'" . $this->escapeString($this->generateTextParagraph($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
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
                        $valList[] = "'" . $this->escapeString($this->generateTextParagraph($columnInfo['minLength'], $columnInfo['maxLength'])) . "'";
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
     * @return Faker The $this class reference for chain-of
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
     * @return Faker The $this class reference for chain-of
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
     * @return Faker The $this class reference for chain-of
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
