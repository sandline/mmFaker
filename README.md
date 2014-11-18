mmFaker
=======

**mmFaker** is a simple tool to generate INSERT INTO .sql files for any table you need to fill with random data.

You don't need to instal it, just put the single mmFaker.php files and *.list files in a folder and run your code including it.

## Example

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
      ->createRows(50)
      ->toFile('./insert_users.sql');
```

## References

setTableName
----

Set the name of the table for wich you're generating inserts

```php
$faker->setTableName($tableName);
```

##### Parameters

>**tableName:** *string* The name of the table for wich you're generating inserts

truncate
----

Add a TRUNCATE statement to the final SQL.

```php
$faker->truncate();
```

##### Parameters

>*none*

