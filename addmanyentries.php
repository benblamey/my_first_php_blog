<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<!-- This page adds a large number of entries so that pagination can be demonstrated. -->
<?php include 'lib/core.php' ?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Many Entries Added...</title>
</head>
<body>
<?php
include 'header.php';

$con = Initialize();

for ($counter = 1; $counter <= 100; $counter++)
{
    $title = 'Title '.$counter;
    $author = 'Joe Bloggs '.$counter;
    $body = $counter;

    mysql_query('INSERT INTO entries (date, title, author, body)
        VALUES (utc_timestamp(), "'
        . $title . '", "'
        . $author . '", "'
        . $body . '")', $con);
}

echo ('Lots of dummy entries should have been added..');

?>
</body>
</html>