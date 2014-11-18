# mmFaker

**mmFaker** is a simple tool to generate INSERT INTO .sql files for any table you need to fill with random data.

You don't need to instal it, just put the single mmFaker.php files and *.list files in a folder and run your code including it.

### Example

*Fill an username table* - This generate random data for a sample user table

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
      /* Generate an email address with length between 10 and 80 */
      ->addMail('user_mail', mmFaker::RANDOM_VALUE, 10, 80)
      /* password is encoded with mysql PASSWORD() function*/
      ->addPassword('user_password', mmFaker::RANDOM_VALUE, true)
      /* Say you want 50 rows */
      ->createRows(50)
      /* ...and just save to a local file. */
      ->toFile('./insert_users.sql');
```

### References

---
##### setTableName

Set the name of the table for wich you're generating inserts

```php
$faker->setTableName($tableName);
```

###### Parameters

>**$tableName:** *string*

>The name of the table for wich you're generating inserts

---
##### truncate

Add a TRUNCATE statement to the final SQL.

```php
$faker->truncate();
```

###### Parameters

>*none*

---
##### addInteger

Add a column definition that generate random/fixed integer values.

```php
$faker->addInteger($fieldName, $generationMode, $minOrFix=null, $max=null);

/* Example - create a column that always contains `1` */
$faker->addInteger('parent_product_id', mmFaker::FIXED_VALUE, 1);
```

###### Parameters

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use **mmFaker::RANDOM_VALUE** if you need a random value, **mmFaker::FIXED_VALUE** if you want a fixed integer value in this field (useful for parent reference fixed id)

>**$minOrFix:** *int*

>If you're generating a random value it's the minimum integer value; if you're generating a fixed value it's the integer fixed value

>**$max:** *int*

>The maximum value or NULL to ignore it (only work if you're generating a random value)

---
##### addDecimal

Add a column definition that generate random/fixed decimal values.

```php
$faker->addDecimal($fieldName, $generationMode, $minOrFix=null, $max=null, $precision=null);

/* Example - create a column that contains decimal with 2 digits between 0 and 15 (eg. 13.24) */
$faker->addDecimal('user_rating', mmFaker::RANDOM_VALUE, 0, 15, 2);
```

###### Parameters

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

---
##### addBitMap

Add a column definition that generate random/fixed bitmap values in b'01001010' format.

The number of string bits is related to min/max parameter (0-255 = 8 bits, 0-65535 = 16 bits and so on).

```php
$faker->addBitMap($fieldName, $generationMode, $minOrFix=null, $max=null);

/* Example - create a column that contains a bitmap value between 0 (b'00000000') and 255 (b'11111111') */
$faker->addBitMap('user_flags', mmFaker::RANDOM_VALUE, 0, 255);
```

###### Parameters

>**$fieldName:** *string*

>The name of the table for wich you're generating inserts

>**$generationMode:** *int*

>Use mmFaker::RANDOM_VALUE if you need a random value, mmFaker::FIXED_VALUE if you want a fixed bitmap value in this field (will be translated to bitmap)

>**$minOrFix:** *int*

>If you're generating a random value it's the minimum bitmap value; if you're generating a fixed value it's the bitmap fixed value

>**$max:** *int*

>The maximum value or NULL to ignore it (only work if you're generating a random value)

### License & Legal
---------------

Copyright 2014 Marco Muracchioli

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this work except in compliance with the License. You may obtain a copy of the License in the LICENSE file, or at:

 |  http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the License for the specific language governing permissions and limitations under the License.
