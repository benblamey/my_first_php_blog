<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php include 'lib/core.php' ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Comment Added</title>
    </head>
    <body>
        <?php

        include 'header.php';

        $con = Initialize();

        // We strip out all but a few harmless tags incase something malicious has got though.
        $entryid = strip_tags(mysql_real_escape_string($_POST['entryid'], $con));
        $author = strip_tags(mysql_real_escape_string($_POST['author'], $con));
        $body = strip_tags(mysql_real_escape_string($_POST['body'], $con), ALLOWED_TAGS);

        if (empty($author)) {
            echo ("An author was not specified.\n");
        } else if (empty($body)) {
            echo ("A body was not specified.\n");
        } else {

            $result = mysql_query('INSERT INTO comments (entryid, date, author, body)
                VALUES ('
                    . '"' . $entryid . '", '
                    . '"timestamp_utc()", '
                    . '"' . $author . '", '
                    . '"' . $body . '")',
                    $con);

            if ($result) {
                echo ("The comment has been added to the entry.</br>\n");
                echo ("<br/><a href='showentry.php?id=$entryid'>View Entry</a>");
            } else {
                echo('An error occured: '.mysql_error($con).'\n');
            }
        }
        ?>
    </body>
</html>
