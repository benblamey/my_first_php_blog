<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<?php
include 'lib/core.php';
$con = Initialize();

$entryid = mysql_real_escape_string($_GET['id'], $con);

if (empty($entryid))
    die("No id was specified");

$result = mysql_query("SELECT *
        FROM entries WHERE id = " . $entryid, $con);

if (!$result) {
    $title = 'Entry Not Found';
} else {
    $row = mysql_fetch_array($result);
    $title = strip_tags($row['title']);
    $author = strip_tags($row['author']);
    $body = strip_tags($row['body'], ALLOWED_TAGS);
}
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title><?php echo($title) ?></title>
</head>
<body>
        
<?php
include 'header.php';

if (!empty($row)) {
    echo '<H1>' . $title . "</H1>\n";
    echo '<P><I> by ' . $author . "</I></P>\n";
    echo '<P>' . $body . "</P>\n";

    $result = mysql_query("SELECT * FROM comments WHERE entryid = "
            . $entryid ." ORDER BY date ASC");
    if (!$result)
        die(mysql_error());

    $havecomments = false;
    while ($commentrow = mysql_fetch_array($result))
    {
        $havecomments= true;
        $commentdate = gmdate("F j, Y, g:i a", strtotime($commentrow['date']));
        $commentauthor = strip_tags($commentrow['author']);
        $commentbody = strip_tags($commentrow['body'], ALLOWED_TAGS);

        echo ("<div id='entry' />\n");
        echo ('<p><i>' . $commentdate . ' - ');
        echo ('by ' . $commentauthor . "</i></p>\n" );

        echo ("<p>\n");
        echo ($commentbody);
        echo ("</p>\n");
        echo ("</div>\n");
    }

    if (!$havecomments)
        echo("<p>No comments - be the first to comment!");

    echo('
        <br/><br/>
        <form action="commentadded.php" method="POST">
        Name<br /><input type="text" name="author" /><br />
        <input type="hidden" name="entryid" value="'.$entryid.'"
        <textarea rows="10" cols="50" name="body"></textarea><br/>
        
        <p>The following tags are allowed in the comment:
        <b>'.ALLOWED_TAGS_HELP.'</b></p>
        <input type="submit" />
    </form>');
}
else
{
    echo "Entry not found.\n";
}

?>
</body>
</html>