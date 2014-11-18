# mmFaker

**mmFaker** is a **simple** tool to generate mysql INSERT INTO .sql files for any table you need to fill with random data.

You don't need to instal it, just put the single mmFaker.php files and *.list files in a folder and run your code including it.

You will have a .sql file that you can run with mysql [database] < file.sql each time you need to refresh your test data.

### Intro

Why another random data generator for mysql?

Actually i've searched for random data generators before write my own, and what I founded was that:

* most of them connect to db and generate data (it's slow!);
* each time I need new data, I had to run again the whole process (it's slow!);
* they are made of a lot of classes, that reflects itself and then look again at the mirror before execute, then decided that it's simpliest to use its interface ancestors BUT only if there is no ancestor that have a common parents with a female interface (simplifying: they are too complex for such a simple task!);
* if I have to share the generated data with another developer, I have to export the full table (i'm lazy!).

So I decided to write a generator of my own, considering my main needs:

* must generate a sql file, so I can run directly with mysql < file.sql (it's faster!);
* i want to use file more and more times because my applications edit the data for testing;
* i want to be able to easily share the generated inserts with other develpers

If you have my same need feel free to use this class for your testing porpouse. **I will update it** each time *I will need a new database random field value* for my tests.

### Index

* [Usage examples](#example)
* [Class constant references](#class-constant-references)
* [Class function references](#class-function-references)
  * [setTableName](#settablename)
  * [truncate](#truncate)
  * [addInteger](#addinteger)
  * [addDecimal](#adddecimal)
  * [addBitMap](#addbitmap)
  * [addIPAddress](#addipaddress)
  * [addTitle](#addtitle)
  * [addText](#addtext)
  * [addUserName](#addusername)
  * [addCreditCard](#addcreditcard)
* [Customizing word files](#customizing-word-files)
* [Todo](#todo)
* [Licensing and legal](#license--legal)

### Example

#### Fill an users table

This generate random data for a sample user table.

Consider the following table:

```sql
CREATE TABLE users (
  users_id BIGINT UNSIGNED AUTO_INCREMENT NOT NULL,

  user_mail VARCHAR(80) NOT NULL,
  user_password VARCHAR(41) NOT NULL,

  PRIMARY KEY (users_id),
  UNIQUE KEY idx_users_usermail (user_mail)

) ENGINE=InnoDB;
```
Now let's create all random users you need for testing:

```php
<?php
require_once './mmFaker.php';

$faker=new mmFaker();

$faker->setTableName('users')
      /* add a TRUNCATE TABLE just before the insert pack */
      ->truncate()
      /* Generate an email address with length between 10 and 20 */
      ->addMail('user_mail', mmFaker::RANDOM_VALUE, 10, 20)
      /* password is encoded with mysql PASSWORD() function if last param is TRUE */
      ->addPassword('user_password', mmFaker::RANDOM_VALUE, 5, 15, true)
      /* Add a random user description text between 30 and 100 characters */
      ->addText('user_description', mmFaker::RANDOM_VALUE, 30, 100)
      /* Say you want 5 rows */
      ->createRows(5)
      /* ...and just save to a local file. */
      ->toFile('./insert_users.sql');
```

This is the generated insert_user.sql file after running the example script:

```sql
TRUNCATE TABLE users;
INSERT INTO users
(user_mail,user_password,user_description)
VALUES
('5WkTM@alice.it',PASSWORD('AA6AVZClX'),'È accaduto più d\'una volta a personaggi di ben più alto affare che don Abbondio, di trovarsi in frangenti'),
('MzVCSY@yahoo.it',PASSWORD('deE1wSTf2z'),'Si racconta che il principe di Condé dormì profondamente la notte avanti la giornata di Rocroi: ma,'),
('ejo08R@alice.it',PASSWORD('TCMwc_fER49V8BJ'),'Non fu però di questo parere l\'Illustrissimo ed Eccellentissimo Signore, il Signor Don Pietro Enriquez'),
('w8Pbl@google.com',PASSWORD('fuR6qw59Y'),'Don Abbondio (il lettore se n\'è già avveduto) non era nato con un cuor di leone. Ma, fin da\' primi'),
('Dythqp@alice.it',PASSWORD('mQzDXP0Tr14V7m'),'Che i due descritti di sopra stessero ivi ad aspettar qualcheduno, era cosa troppo evidente; ma quel');
```

Now you can import new sql data to your favorite database:

```bash
mysql -uroot -pyourmysqlpassword destination_database < insert_users.sql
```

Back to [index](#index "Back to index") \| [top](# "Back to top")

#### Generate multiple rows changing only one fields every *n* rows

You can redefine a field after the first call to `generateRows()` if you need to change that field value every *n* rows.

Consider the table from previous example and the following snippet as generator:

```php
$faker->setTableName('users')
      /* add a TRUNCATE TABLE just before the insert pack */
      ->truncate()
      /* Generate an email address with length between 10 and 20 */
      ->addMail('user_mail', mmFaker::RANDOM_VALUE, 10, 20)
      /* password is encoded with mysql PASSWORD() function if last param is TRUE */
      ->addPassword('user_password', mmFaker::RANDOM_VALUE, 5, 15, true)
      /* Add a fixed user description */
      ->addText('user_description', mmFaker::FIXED_VALUE, 'This user is in the first pack of 5')
      /* Say you want 5 rows */
      ->createRows(5)
      /* Replace the fixed user description for next 5 rows */
      ->addText('user_description', mmFaker::FIXED_VALUE, 'This user is in the second pack of 5')
      /* 5 more rows */
      ->createRows(5)
      /* Replace the fixed user description for next 5 rows */
      ->addText('user_description', mmFaker::FIXED_VALUE, 'This user is in the third pack of 5')
      /* 5 more rows */
      ->createRows(5)
      /* ...and just save to a local file. */
      ->toFile('./insert_users.sql');
```

This will create a 15 rows insert where:

* rows 1-5 will have "This user is in the first pack of 5" in `user_description` column;
* rows 6-10 will have "This user is in the second pack of 5" in `user_description` column;
* rows 11-15 will have "This user is in the third pack of 5" in `user_description` column.

That's the output:

```sql
TRUNCATE TABLE users;
INSERT INTO users
(user_mail,user_password,user_description)
VALUES
('y1eAZ@shortmail.com',PASSWORD('yw_h3IHdFF'),'This user is in the first pack of 5'),
('3VxrO@email.it',PASSWORD('aP0S1'),'This user is in the first pack of 5'),
('mOzNA@inbox.com',PASSWORD('.W7gNDOR9gjx-4U'),'This user is in the first pack of 5'),
('ZpiM7@libero.it',PASSWORD('zpjaPWALWkG3Y9E'),'This user is in the first pack of 5'),
('KLqID@infinito.it',PASSWORD('W9W.ew6bWjlWZ7k'),'This user is in the first pack of 5');
('v4Hg0_J@tin.it',PASSWORD('Jh9eK71HR0Ma'),'This user is in the second pack of 5'),
('X3YGPT@gmx.com',PASSWORD('ax.44fS3OzzW'),'This user is in the second pack of 5'),
('uqs17f@tin.it',PASSWORD('8ZbSQ0mtH'),'This user is in the second pack of 5'),
('42lW8@yandex.com',PASSWORD('MJq.tNDN3VCY13'),'This user is in the second pack of 5'),
('4ArSNL@gmx.com',PASSWORD('D4dtd0p'),'This user is in the second pack of 5');
('ZlzfY@yandex.com',PASSWORD('TOLiAz_ha3IsIi'),'This user is in the third pack of 5'),
('LVZ5Y@yandex.com',PASSWORD('hT.64z.PpSr'),'This user is in the third pack of 5'),
('RnZZo@inbox.com',PASSWORD('uGmCx7bfT73t'),'This user is in the third pack of 5'),
('t8BT7@google.it',PASSWORD('8NvWB'),'This user is in the third pack of 5'),
('oZl4g@shortmail.com',PASSWORD('eJc.LhTF_aHI'),'This user is in the third pack of 5');
```

Back to [index](#index "Back to index") \| [top](# "Back to top")

### Class constant references

The following constant are defined in class and can be used as parameters for function calls:

##### mmFaker::RANDOM_VALUE

Used to specify the $generationMode for various class functions. Definition:

```php
  const RANDOM_VALUE          = 1;
```

##### mmFaker::FIXED_VALUE

Used to specify the $generationMode for various class functions. Definition:

```php
  const FIXED_VALUE           = 2;
```

##### mmFaker::CARD_VISA

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_VISA             = 16;
```

##### mmFaker::CARD_VISA13

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_VISA13           = 13;
```

##### mmFaker::CARD_DINERS

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_DINERS           = 14;
```

##### mmFaker::CARD_MASTERCARD

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_MASTERCARD       = 16;
```

##### mmFaker::CARD_AMERICANEXPRESS

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_AMERICANEXPRESS  = 15;
```

##### mmFaker::CARD_JCB

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_JCP              = 16;
```

##### mmFaker::CARD_DISCOVER

Used to specify the $cardType for `addCreditCard()` function. Definition:

```php
  const CARD_DISCOVER         = 16;
```

Back to [index](#index "Back to index") \| [top](# "Back to top")

### Class function references

**WARNING:** All "add" functions create a new field definition. If you use the same field name more than one time, the last definition will replace all previous definitions. Example:

```php
<?php
require_once './mmFaker.php';

$faker=new mmFaker();

$faker->setTableName('users')
      ->truncate()
      ->addMail('field_name', mmFaker::RANDOM_VALUE, 10, 20)
      ->addPassword('field_name', mmFaker::RANDOM_VALUE, 5, 15, true)
      ->addText('field_name', mmFaker::RANDOM_VALUE, 30, 100)
      ->createRows(5)
      ->toFile('./insert_users.sql');
```

This snippet of code will generate inserts with only one field, named field_name that contains random text (last addText() call).

---
##### setTableName

Set the name of the table for wich you're generating inserts

```php
$faker->setTableName($tableName);
```

###### Parameters for setTableName

>**$tableName:** *string*

>The name of the table for wich you're generating inserts

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### truncate

Add a TRUNCATE statement as fist row in the generated SQL.

```php
$faker->truncate();
```

###### Parameters for truncate

>*none*

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addInteger

Add a column definition that generate random/fixed integer values.

```php
$faker->addInteger($fieldName, $generationMode, $minOrFix=null, $max=null);

/* Example - create a column that always contains `1` */
$faker->addInteger('parent_product_id', mmFaker::FIXED_VALUE, 1);
```

###### Parameters for addInteger

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use **mmFaker::RANDOM_VALUE** if you need a random value, **mmFaker::FIXED_VALUE** if you want a fixed integer value in this field (useful for parent reference fixed id)

>**$minOrFix:** *int*

>If you're generating a random value it's the minimum integer value; if you're generating a fixed value it's the integer fixed value

>**$max:** *int*

>The maximum value or NULL to ignore it (only work if you're generating a random value)

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addDecimal

Add a column definition that generate random/fixed decimal values.

```php
$faker->addDecimal($fieldName, $generationMode, $minOrFix=null, $max=null, $precision=null);

/* Example - create a column that contains decimal
   with 2 digits between 0 and 15 (eg. 13.24) */
$faker->addDecimal('user_rating', mmFaker::RANDOM_VALUE, 0, 15, 2);
```

###### Parameters for addDecimal

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed integer value in this field (useful for parent reference fixed id)

>**$minOrFix:** *int*

>If you're generating a random value it's the minimum decimal value; if you're generating a fixed value it's the decimal fixed value

>**$max:** *int*

>The maximum value or NULL to ignore it (only work if you're generating a random value)

>**$precision:** *int*

>The decimal precision

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addBitMap

Add a column definition that generate random/fixed bitmap values in b'01001010' format.

The number of string bits is related to min/max parameter (0-255 = 8 bits, 0-65535 = 16 bits and so on).

```php
$faker->addBitMap($fieldName, $generationMode, $minOrFix=null, $max=null);

/* Example - create a column that contains a bitmap value
   between 0 (b'00000000') and 255 (b'11111111') */
$faker->addBitMap('user_flags', mmFaker::RANDOM_VALUE, 0, 255);
```

###### Parameters for addBitMap

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$minOrFix:** *int*

>If you're generating a random value it's the minimum bitmap value; if you're generating a fixed value it's the bitmap fixed value

>**$max:** *int*

>The maximum value or NULL to ignore it (only work if you're generating a random value)

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addIPAddress

Add a column definition that generate random/fixed ip address values in IPv4, IPv6 or both formats at the same time.

```php
$faker->addIPAddress($fieldName, $generationMode, $ipv4=true, $ipv6=false, $fixedValue=null);

/* Example - create a column that contains random ip address
   only in IPv4 format */
$faker->addIPAddress('last_ip', mmFaker::RANDOM_VALUE, true, false);
```

###### Parameters for addIPAddress

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$ipv4:** *bool*

>Set this to TRUE if you want to generate IPv4 addresses

>**$ipv6:** *bool*

>Set this to TRUE if you want to generate IPv6 addresses

>**$ipv6:** *string*

>the fixed value to use if you specify $generationMode=mmFaker::FIXED_VALUE

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addTitle

Add a column definition that generate random/fixed text title useful for fields that contains h1, h2 ... h6 and so on. This may be used also for image descriptions, meta description/keyword values etc.

Titles does not have minimum/maximum length; look at [titles.list for addTitle](#titleslist-for-addtitle) paragraph.

```php
$faker->addTitle($fieldName, $generationMode, $fixedValue=null);

/* Example - create a column that contains random titles */
$faker->addTitle('article_title', mmFaker::RANDOM_VALUE);
```

###### Parameters for addTitle

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$fixedValue:** *string*

>If $generationMode is set to mmFaker::FIXED_VALUE this will be the fixed value for field.

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addText

Add a column definition that generate random/fixed text paragraph with variable length. If the random paragraph used is larger than maximum length, will be truncated at the nearest space.

eg: if you require 200 as max length and the paragraph is 250 characters and have a space ad position 197 and 204, the result will be the first 196 characters of random picked paragraph.

```php
$faker->addText($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null);

/* Example - create a column that contains random text with
   length between 50 and 150 characters */
$faker->addText('article_body', mmFaker::RANDOM_VALUE, 50, 150);
```

###### Parameters for addText

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$minLengthOrFix:** *int*|*string*

>If you're generating a random value it's the minimum text length; if you're generating a fixed value it's the text fixed value

>**$maxLength:** *int*

>The maximum length or NULL to ignore it (only work if you're generating a random value)

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
##### addUserName

Add a column definition that generate random/fixed user name with variable length.

User names is get directly from usernames.list files: look at [usernames.list for addTitle](#usernameslist-for-adduser) paragraph.

```php
$faker->addUserName($fieldName, $generationMode, $minLengthOrFix=null, $maxLength=null);

/* Example - create a column that contains random user name
   with length between 5 and 15 characters */
$faker->addUserName('user_name', mmFaker::RANDOM_VALUE, 5, 15);
```

###### Parameters for addUserName

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$minLengthOrFix:** *int*|*string*

>If you're generating a random value it's the minimum user name length; if you're generating a fixed value it's the user name fixed value

>**$maxLength:** *int*

>The maximum length or NULL to ignore it (only work if you're generating a random value)

Back to [index](#index "Back to index") \| [top](# "Back to top")

##### addCreditCard

Generate a TOTALLY RANDOM credit card number, not useful for anything that is not fill a database field. Still, the number is calculated to succesfully pass the CCChecksum and Luhn Check Digit algorithm (I just check it for 16 digits card number).

```php
$faker->addCreditCard($fieldName, $cardType=self::CARD_VISA);

/* Example - create an American Express 15 digits card number */
$faker->addCreditCard('user_card', mmFaker::CARD_AMERICANEXPRESS);
```

###### Parameters for addUserName

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$cardType:** *int*

>The card type (only impact on length - actually constants ARE defined as integer value and used as number length)

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
### Customizing word files

Included in the package you find a pack of .list files that is used as source for random texts.

All files are (must be) in **utf8** format with **linux \n** line terminator. All files is loaded ONLY if you're using the related function (eg. if you use addText paragraph.list will be loaded).

**NOTICE:** the bigger you make the word files, the highest memory footprint will have the mmFaker while running.

###### paragraph.list for addText

This contains a set of random text paragraph and it's used by addText function.

You can put one paragraph for row; actually it's filled with paragraph from "I promessi sposi" di Alessandro Manzoni.

###### email_servers.list for addMail

This contains a set of "valid" email server (eg. @google.com). The mail username will be always generated using random chars. Server may be of course "invalid" too, such as @thismailserverdoesnotexists.com

You can put one valid server (always start with @ symbol) per row.

###### titles.list for addTitle

This contains a set of suitable titles for "short" titled text generation (useful for fields that contains h1, h2 etc.).

You can put one title for row; actually it's filled with titles generated by single period from lorem ipsum.

###### usernames.list for addUser

This contains a set of random user names.

You can put one user name for row; actually it's filled with real user names got from online forums (beware: I didn't check it, so I don't know if there is some user name that may result offensive).

Back to [index](#index "Back to index") \| [top](# "Back to top")

---
### Todo

For the moment just better phpdoc and some small fixes.

---
### License & Legal

Copyright 2014 Marco Muracchioli

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

 |  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.

Back to [index](#index "Back to index") \| [top](# "Back to top")
