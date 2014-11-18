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
 * By removing or commenting line 22 (that begin with Die) you accept
 * the licence conditions.
 */
//Die("\n\nmmFaker: you must accept the license conditions before use this class. See mmFaker.php source file and read comments.\n\n\n");

/**
 * Fake Data Generation Toolkit Class
 *
 * @author Marco Muracchioli <marco.muracchioli@gmail.com>
 * @copyright (C) 2014 Marco Muracchioli
 * @license Apache License 2.0 <http://www.apache.org/licenses/LICENSE-2.0>
 */
class mmFaker {

  const TYPE_INTEGER    = 1;
  const TYPE_IPADDRESS  = 2;
  const TYPE_DECIMAL    = 3;
  const TYPE_USERNAME   = 4;
  const TYPE_MAIL       = 5;
  const TYPE_TEXT       = 6;
  const TYPE_BITMAP     = 7;
  const TYPE_PASSWORD   = 8;
  const TYPE_CREDITCARD = 9;

  const RANDOM_VALUE    = 1;
  const FIXED_VALUE     = 2;

  const CARD_VISA             = 16;
  const CARD_VISA13           = 13;
  const CARD_DINERS           = 14;
  const CARD_MASTERCARD       = 16;
  const CARD_AMERICANEXPRESS  = 15;
  const CARD_JCP              = 16;
  const CARD_DISCOVER         = 16;

  private $userNames          = null;
  private $textParagraph      = null;
  private $textTitles         = null;
  private $emailServers       = null;
  private $validUserNameChars = array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9','-','_','.');

  protected $tableName=null;
  protected $truncateTable=null;
  protected $columns=array();
  protected $values=array();
  protected $rows=array();

  protected $totUserNames=0;
  protected $totTextParagraph=0;
  protected $totTextTitles=0;
  protected $totEmailServers=0;

  public function __construct() {
    $this->totValidUserNameChars=count($this->validUserNameChars);
  }

  public function __destruct() {
  }

  /**
   * Escape a string for mysql
   *
   * @param string $str The string to escape
   * @return string The escaped string
   * @since 1.0
   * @access protected
   */
  protected function escapeString($str) {
    return str_replace(array("\\","\x00","\n","\r","'",'"',"\x1a"), array("\\\\","\\0","\\n","\\r","\'",'\"',"\\Z"), $str);
  }

  /**
   * Set the table name for inserts; you can also specify database.table if you want
   *
   * @param string $name The table name to use
   * @return mmFaker The $this class reference for chain-of
   * @since 1.0
   * @access public
   */
  public function setTableName($name) {
    $this->tableName=$name;
    return $this;
  }

  /**
   * Complete reset of the engine (rows, fields, loaded word files)
   *
   * @return mmFaker The $this class reference for chain-of
   * @since 1.0
   * @access public
   */
  public function reset() {
    unset($this->columns, $this->userNames, $this->textParagraph, $this->textTitles, $this->emailServers);
    $this->columns=array();
    $this->totUserNames=0;
    $this->totTextParagraph=0;
    $this->totTextTitles=0;
    $this->totEmailServers=0;
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
  public function truncate() {
    $this->truncateTable=true;
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
  public function addInteger($fieldName, $generationMode, $minOrFix=null, $max=null) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_INTEGER,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minOrFix,
      "min"       => $generationMode==self::RANDOM_VALUE?$minOrFix:null,
      "max"       => $generationMode==self::RANDOM_VALUE?$max:null,
    );
    return $this;
  }

  /**
   * Generate a random integer number
   *
   * @see addInteger
   * @return float The integer number
   * @since 1.0
   * @access protected
   */
  protected function generateInteger($min,$max) {
    return rand($min,$max);
  }

  public function addIPAddress($fieldName, $generationMode, $ipv4=true, $ipv6=false, $fixedValue=null) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_IPADDRESS,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "ipv4"      => $generationMode==self::RANDOM_VALUE?$ipv4:true,
      "ipv6"      => $generationMode==self::RANDOM_VALUE?$ipv6:true,
      "fixValue"  => $fixedValue,
    );
    return $this;
  }

  protected function generateIPAddress($ipv4=true, $ipv6=false){
    switch (true) {
      case ($ipv4 && $ipv6):
        if (rand(0,1)==1) {
          // IPV6
          return str_replace(array(':::','::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535))))));
        } else {
          // IPV4
          return implode('.', array(rand(0,220),rand(0,255),rand(0,255),rand(0,254)));
        }
        break;
      case ($ipv4):
        return implode('.', array(rand(0,220),rand(0,255),rand(0,255),rand(0,254)));
        break;
      case ($ipv6):
        return str_replace(array(':::','::::'), '::', str_replace(':0:', '::', implode(':', array(dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535)),dechex(rand(0,65535))))));
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
  public function addDecimal($fieldName, $generationMode, $minOrFix=null, $max=null, $precision=null) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_DECIMAL,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minOrFix,
      "min"       => $generationMode==self::RANDOM_VALUE?$minOrFix:null,
      "max"       => $generationMode==self::RANDOM_VALUE?$max:null,
      "precision" => $precision,
    );
    return $this;
  }

  /**
   * Generate a random decimal number
   *
   * @see addDecimal
   * @return float The decimal number
   * @since 1.0
   * @access protected
   */
  protected function generateDecimal($min, $max, $precision) {
    $num=(float)rand()/(float)getrandmax();
    return round($num*($max-$min)+$min, $precision);
  }

  public function addPassword($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null, $encoded=false) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_PASSWORD,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minLengthOrFix,
      "minLength" => $generationMode==self::RANDOM_VALUE?$minLengthOrFix:null,
      "maxLength" => $generationMode==self::RANDOM_VALUE?$maxLength:null,
      "encoded"   => $encoded,
    );
    return $this;
  }

  protected function generatePassword($minLen=null, $maxLen=null) {
    switch (true) {
      case (isset($minLen) && isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return ($len>=$min) && ($len<=$max); };
        break;
      case (isset($minLen)):
        $checkFunc=function($len,$min,$max){ return $len>=$min; };
        break;
      case (isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return $len<=$max; };
        break;
      default:
        $checkFunc=function($len,$min,$max){ return true; };
    } // switch
    $pwd="";
    $upwd=rand($minLen,$maxLen);
    for ($i=0;$i<$upwd;$i++) {
      $pwd.=$this->validUserNameChars[rand(0,$this->totValidUserNameChars-1)];
    }
    return $pwd;
  }

  public function addUserName($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null) {
    if ($this->totUserNames==0) {
      $this->userNames=explode("\n",file_get_contents('usernames.list'));
      $this->totUserNames=count($this->userNames)-1;
    }
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_USERNAME,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minLengthOrFix,
      "minLength" => $generationMode==self::RANDOM_VALUE?$minLengthOrFix:null,
      "maxLength" => $generationMode==self::RANDOM_VALUE?$maxLength:null,
    );
    return $this;
  }

  protected function generateUserName($minLen=null, $maxLen=null) {
    switch (true) {
      case (isset($minLen) && isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return ($len>=$min) && ($len<=$max); };
        break;
      case (isset($minLen)):
        $checkFunc=function($len,$min,$max){ return $len>=$min; };
        break;
      case (isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return $len<=$max; };
        break;
      default:
        $checkFunc=function($len,$min,$max){ return true; };
    } // switch
    $itemId=rand(1,$this->totUserNames)-1;
    while(!$checkFunc(strlen($this->userNames[$itemId]),$minLen,$maxLen)) {
      $itemId=rand(1,$this->totUserNames)-1;
    }
    return $this->userNames[$itemId];
  }

  public function addMail($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null) {
    if ($this->totEmailServers==0) {
      $this->emailServers=explode("\n",file_get_contents('./email_servers.list'));
      $this->totEmailServers=count($this->emailServers)-1;
    }
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_MAIL,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minLengthOrFix,
      "minLength" => $generationMode==self::RANDOM_VALUE?$minLengthOrFix:null,
      "maxLength" => $generationMode==self::RANDOM_VALUE?$maxLength:null,
    );
    return $this;
  }

  protected function generateMail($minLen,$maxLen) {
    $serverID=rand(1,$this->totEmailServers)-1;
    switch (true) {
      case (isset($minLen) && isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return ($len>=$min) && ($len<=$max); };
        break;
      case (isset($minLen)):
        $checkFunc=function($len,$min,$max){ return $len>=$min; };
        break;
      case (isset($maxLen)):
        $checkFunc=function($len,$min,$max){ return $len<=$max; };
        break;
      default:
        $checkFunc=function($len,$min,$max){ return true; };
    } // switch
    $uname="";
    $unameLen=rand(5,$maxLen-strlen($this->emailServers[$serverID])-5);
    $unameLen=$unameLen<5?5:$unameLen;
    for ($i=0;$i<$unameLen;$i++) {
      $uname.=$this->validUserNameChars[rand(0,$this->totValidUserNameChars-1)];
    }
    return str_replace('..', '.', $uname.$this->emailServers[$serverID]);
  }

  public function addText($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null) {
    if ($this->totTextParagraph==0) {
      $this->textParagraph=explode("\n",file_get_contents('./paragraph.list'));
      $this->totTextParagraph=count($this->textParagraph)-1;
      echo "Loaded {$this->totTextParagraph} text paragraph.\n";
    }
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_TEXT,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minLengthOrFix,
      "minLength" => $generationMode==self::RANDOM_VALUE?$minLengthOrFix:null,
      "maxLength" => $generationMode==self::RANDOM_VALUE?$maxLength:null,
    );
    return $this;
  }

  protected function generateText($minLen=null, $maxLen=null) {
    $text="";
    $done=false;
    while (!$done) {
      $text.=$this->textParagraph[rand(0,$this->totTextParagraph-1)];
      $textLen=strlen($text);
      if ((isset($minLen) && ($textLen>=$minLen)) && isset($maxLen)) {
        if ($textLen>=$maxLen) {
          $text=substr($text,0,strpos($text,' ',$maxLen));
          $done=true;
        }
      } elseif (isset($minLen) && ($textLen>=$minLen)) {
        $done=true;
      } elseif (isset($maxLen) && ($textLen<=$maxLen)) {
        $text=substr($text,0,strpos($text,' ',$maxLen));
        $done=true;
      } else {
        $done=true;
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
  public function addBitMap($fieldName, $generationMode, $minOrFix=null, $max=null) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_BITMAP,
      "random"    => $generationMode==self::RANDOM_VALUE?true:false,
      "fixValue"  => $generationMode==self::RANDOM_VALUE?null:$minOrFix,
      "min"       => $generationMode==self::RANDOM_VALUE?$minOrFix:null,
      "max"       => $generationMode==self::RANDOM_VALUE?$max:null,
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
  protected function generateBitMap($min,$max) {
    $maxStr=strlen(decbin($max));
    $realLen=(round($maxStr)%8===0)?round($maxStr):round(($maxStr+8/2)/8)*8;
    return sprintf( "%0".$realLen."d", decbin(rand($min,$max)));
  }

  public function addCreditCard($fieldName, $cardType) {
    $this->columns[$fieldName]=array(
      "type"      => self::TYPE_CREDITCARD,
      "random"    => false,
      "numberLen" => $cardType,
    );
    return $this;
  }

  protected function generateCreditCard($cardType) {
    $ccn="";
    $cca=array('0','1','2','3','4','5','6','7','8','9');
    $v=0;
    for ($i=0;$i<$cardType-1;$i++) {
      $c=$cca[rand(0,9)];
      echo "char ".($cardType-$i)." is {$c}\n";
      $n=(int)$c;
      if ($i & 1) {
        $v+=$n;
      } else {
        $v+=($n*2>9)?$n*2-9:$n*2;
      }
      $ccn=$c.$ccn;
    }
    $ccn.=10-$v%10;
    return $ccn;
  }

  /**
   * Generate one insert row using user-added rules
   *
   * @return string A single insert row
   * @since 1.0
   * @access protected
   */
  protected function createRow() {
    $valList=array();
    foreach ($this->columns as $fieldName => $columnInfo) {
      if ($columnInfo['random']) {
        switch ($columnInfo['type']) {
          case self::TYPE_INTEGER:
            $valList[]=$this->escapeString($this->generateInteger($columnInfo['min'],$columnInfo['max']));
            break;
          case self::TYPE_IPADDRESS:
            $valList[]="'".$this->escapeString($this->generateIPAddress($columnInfo['ipv4'],$columnInfo['ipv6']))."'";
            break;
          case self::TYPE_DECIMAL:
            $valList[]=$this->escapeString($this->generateDecimal($columnInfo['min'],$columnInfo['max'],$columnInfo['precision']));
            break;
          case self::TYPE_USERNAME:
            $valList[]="'".$this->escapeString($this->generateUserName($columnInfo['minLength'], $columnInfo['maxLength']))."'";
            break;
          case self::TYPE_MAIL:
            $valList[]="'".$this->escapeString($this->generateMail($columnInfo['minLength'], $columnInfo['maxLength']))."'";
            break;
          case self::TYPE_TEXT:
            $valList[]="'".$this->escapeString($this->generateText($columnInfo['minLength'], $columnInfo['maxLength']))."'";
            break;
          case self::TYPE_BITMAP:
            $valList[]="b'".$this->escapeString($this->generateBitMap($columnInfo['min'],$columnInfo['max']))."'";
            break;
          case self::TYPE_PASSWORD:
            if ($columnInfo['encoded']) {
              $valList[]="PASSWORD('".$this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength']))."')";
            } else {
              $valList[]="'".$this->escapeString($this->generatePassword($columnInfo['minLength'], $columnInfo['maxLength']))."'";
            }
            break;
          case self::TYPE_CREDITCARD:
            $valList[]="'".$this->escapeString($this->generateText($columnInfo['minLength'], $columnInfo['maxLength']))."'";
            break;
        } // switch
      } else {
        switch ($columnInfo['type']) {
          case self::TYPE_INTEGER:
          case self::TYPE_DECIMAL:
            $valList[]=$this->escapeString($columnInfo['fixValue']);
            break;
          case self::TYPE_IPADDRESS:
          case self::TYPE_USERNAME:
          case self::TYPE_MAIL:
          case self::TYPE_TEXT:
            //var_dump($columnInfo['fixValue']);
            $valList[]="'".$this->escapeString($columnInfo['fixValue'])."'";
            break;
          case self::TYPE_BITMAP:
            $valList[]="b'".$this->escapeString(decbin($columnInfo['fixValue']))."'";
            break;
        } // switch
      }
    }
    $this->values[]=$valList;
  }

  /**
   * Generate requested number of rows and store in internal rows array
   *
   * @param string $totalRows The total number of rows to generate
   * @return mmFaker The $this class reference for chain-of
   * @since 1.0
   * @access public
   */
  public function createRows($totalRows) {
    $from=count($this->values);
    $to=count($this->values)+$totalRows;
    for($i=$from;$i<$to;$i++) {
      echo ".";
      $this->createRow();
    }
    for($i=$from;$i<$to-1;$i++) {
      $this->rows[]="(".implode(',',$this->values[$i])."),";
    }
    $this->rows[]="(".implode(',',$this->values[$to-1]).");";
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
  public function emptyRows() {
    unset($this->rows, $this->values);
    $this->rows=array();
    $this->values=array();
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
  public function toFile($fileName) {
    $baseInsert="";
    if ($this->truncateTable) {
      $baseInsert="TRUNCATE TABLE {$this->tableName};\n";
    }
    $baseInsert.="INSERT INTO {$this->tableName}\n(";
    foreach ($this->columns as $fieldName => $columnInfo) {
      $baseInsert.="{$fieldName},";
    }
    $baseInsert=substr($baseInsert,0,-1).")\nVALUES\n";
    file_put_contents($fileName, $baseInsert.implode("\n",$this->rows));
    return $this;
  }

}
