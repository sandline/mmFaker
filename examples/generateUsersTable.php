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



require "../mmFaker.php";

$faker=new mmFaker();

$faker->setTableName('users')
      ->truncate()
      ->addMail('user_email', mmFaker::RANDOM_VALUE, 15, 35)
      ->addPassword('user_pwd', mmFaker::RANDOM_VALUE, 5, 15, mmFaker::PWD_SHA2_256)
      ->addPersonName('user_name')
      ->addPersonSurname('user_surname')
      ->addIPAddress('lastlogin_ip')
      ->addInteger('lastlogin_ts', mmFaker::RANDOM_VALUE, 1609459200, 1667055202)
      ->createRows(250)
      ->toFile('./insert_users.sql');