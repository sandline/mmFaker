<?php
/*
Example for table users with following fields:
    users_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    user_email VARCHAR(80) NOT NULL,
    user_pwd VARCHAR(64) NOT NULL,
    user_name VARCHAR(35),
    user_surname VARCHAR(50),
    lastlogin_ip VARCHAR(45),
    lastlogin_ts BIGINT UNSIGNED
*/

require_once "../vendor/autoload.php";

use mmFaker\Faker;

$faker=new Faker();
$faker
      ->setList(Faker::LISTTYPE_USERNAMES,      '../src/lists/usernames.list')
      ->setList(Faker::LISTTYPE_MAILSERVERS,    '../src/lists/email_servers.list')
      ->setList(Faker::LISTTYPE_PARAGRAPHS,     '../src/lists/paragraphs.list')
      ->setList(Faker::LISTTYPE_PERSONNAMES,    '../src/lists/italian_names.list')
      ->setList(Faker::LISTTYPE_PERSONSURNAMES, '../src/lists/italian_surnames.list')
      ->setList(Faker::LISTTYPE_TITLES,         '../src/lists/titles.list')
      ->setTableName('users')
      ->truncate()
      ->addMail('user_email', Faker::RANDOM_VALUE, 15, 35)
      ->addPassword('user_pwd', Faker::RANDOM_VALUE, 5, 15, Faker::PWD_SHA2_256)
      ->addPersonName('user_name')
      ->addPersonSurname('user_surname')
      ->addIPAddress('lastlogin_ip')
      ->addInteger('lastlogin_ts', Faker::RANDOM_VALUE, 1609459200, 1667055202)
      ->createRows(250)
      ->toFile('./insert_users.sql');