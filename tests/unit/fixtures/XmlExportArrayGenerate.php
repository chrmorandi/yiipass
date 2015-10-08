<?php
/**
 * This is a simple script to generate some random data as XmlExport Fixture.
 */

$password_entries = array();

for($i = 0; $i <= 100; $i++){
    $password_entries[] = [
        'id' => $i,
        'title' => 'some password' . rand(0, 100),
        'group' => 'some group' . rand(0, 10),
        'username' => 'some username' . rand(0, 100),
        'password' => 'some password' . rand(0, 100),
        'comment' => 'some comment' . rand(0, 100),
        'url' => 'http://some-url.com',
        'creation' => '2015-07-25 ' . rand(0, 23) . ':26:10',
        'lastaccess' => '2015-07-28 ' . rand(0, 23) . ':44:17',
        'lastmod' => '2015-07-26 ' . rand(0, 23) . ':32:33',
        'expire' => null
    ];
}

file_put_contents("XmlExportArr.php",serialize($password_entries));