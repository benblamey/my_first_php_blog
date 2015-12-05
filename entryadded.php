<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php include 'lib/core.php' ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Entry Added</title>
    </head>
    <body>
        <?php
        include 'header.php';

        $con = Initialize();

        // We strip out all but a few harmless tags incase something malicious has been specifed.
        $title = strip_tags(mysql_real_escape_string($_POST['title'], $con));
        $author = strip_tags(mysql_real_escape_string($_POST['author'], $con));
        $body = strip_tags(mysql_real_escape_string($_POST['body'], $con), ALLOWED_TAGS);

        if (empty($title)) {
            echo ("A title was not specified.\n");
        } else if (empty($author)) {
            echo ("An author was not specified.\n");
        } else if (empty($body)) {
            echo ("A body was not specified.\n");
        } else {
            $result = mysql_query('INSERT INTO entries (date, title, author, body)
                VALUES (utc_timestamp(), "'
                . $title . '", "'
                . $author . '", "'
                . $body . '")', $con);

            if ($result) {
                echo ("The entry has been added to the blog.</br>\n");
            } else {
                echo('An error occured: '.mysql_error($conn).'\n');
            }
        }
        ?>
    </body>
</html>
