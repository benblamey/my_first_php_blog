<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
include 'lib/core.php';
$con = Initialize();

// Count the number of blog entries.
$result = mysql_query("SELECT SUM(1) FROM entries");
if (!$result) {
    die(mysql_error($con));
}
$row = mysql_fetch_array($result);
$count = $row[0];

// Determine the number of pages, and which page is selected.
if ($count > 0) {
    $pagecount = ceil($count / ENTRIES_PER_PAGE);

    if (isset($_GET['page'])) {
        // A specific page has been requested.
        $pagenumber = intval($_GET['page']);

        // Ensure the requested page index refers to an existant page.
        if ($pagenumber < 1) {
            $pagenumber = 1;
        } else if ($pagenumber > $pagecount) {
            $pagenumber = $pagecount;
        }
    } else {
        // A specific page has not been requested, default to the first page.
        $pagenumber = 1;
    }

    // Compute the ID of the first entry on this page.
    $startid = ENTRIES_PER_PAGE * ($pagenumber - 1);
    $entries = mysql_query('SELECT * FROM entries '
                    . ' ORDER BY id DESC'
                    . ' LIMIT ' . ENTRIES_PER_PAGE
                    . ' OFFSET ' . $startid, $con);
    if (!$entries) {
        die(mysql_error($con));
    }

} else {
    // There are no entries.
    $pagecount = 0;
    $pagenumber = 0;
}
?>

<html>
    <head>
        <link rel="stylesheet" type="text/css" href="css/indexstyles.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Ben's Blog</title>
    </head>
    <body>


<div id="main">
<?php

include 'header.php';

EchoPaginationLinks($pagecount, $pagenumber);

echo("<br/><br/>");

if ($count > 0) {
    // Echo the blog entries.
    while ($row = mysql_fetch_array($entries)) {
        $entrydate = gmdate("F j, Y, g:i a", strtotime($row['date']));
        $entrytitle = $row['title'];
        $entryauthor = $row['author'];
        $entrybody = $row['body'];

        echo ("<div id='entry' />\n");
        echo ('<h2>' . $entrytitle . "</h2>\n");

        echo ('<p><i>' . $entrydate . ' - ');
        echo ('by ' . $entryauthor . '</i> - ' );
        echo ('<a href="showentry.php?id=' . $row['id'] . "\">Show Entry</a></p>\n");

        echo ("<p>\n");
        echo ($entrybody);
        echo ("</p>\n");

        echo ("</div>\n");
    }
} else {
    echo("<p>There are no entries in this blog.</p>");
}

echo("<br/><br/>");

EchoPaginationLinks($pagecount, $pagenumber);

?>
</div>

<div id="tweets">
    <div align="center">
        <h1>Recent Tweets by the author</h1>
    </div>
            
<?php
// We want to display a list of the authors recent tweets.

// Retrieving the list of tweets from the twitter servers can take around 10
// seconds, and we are not supposed to call the twitter API more often than
// every 60 seconds.
// Therefore, we need to cache the tweets. The first thing to work out
// is whether we need to retrieve a fresh set of tweets.

$result = mysql_query("SELECT * FROM tweets", $con);
if (!$result)
    die (mysql_error($conn));

$row = mysql_fetch_array($result);
$retrieve = false;
if (!$row) {
    // We don't have a cached response - do a get.
    $retrieve = true;
} else {
    // Dates in the MySQL server are in UTC, but the date string does
    // not include any timezone information, so the date is parsed as if it
    // were from a different timezone. We must add the offset to get the date
    // back into UTC.
    $lastretrievedtime = strtotime($row['date']) + date("Z");

    // Retrieve the fresh set of tweets only if the specified interval has elapsed.
    $retrieve = (time() - $lastretrievedtime) > TWITTER_MINIMUM_REQUEST_INTERVAL;
}

if ($retrieve) {
    // Retrieve an up-to-date list of tweets.
    // Depends on the 'pecl_http' extension for PHP.
    // We use '@' to suppress warnings - as sometimes the request times out.
    $response = @http_get(TWITTER_GET_STATUSES_JSON, array(
        "timeout"=>TWITTER_GET_TIMEOUT_SECONDS,
        "connecttimeout"=>TWITTER_CONNECT_TIMEOUT_SECONDS,
        ), $info);

    if (!$response) {
        echo ('Timed out waiting for a response from the Twitter server.');
    } else {
        // Parse the HTTP response.
        $responseBody = http_parse_message($response)->body;

        // Delete any previously cached result, and store the new response.
        $result = mysql_query("DELETE FROM tweets");
        if (!$result)
            die(mysql_error($con));

        $result = mysql_query("INSERT INTO tweets (date, body)
            VALUES (utc_timestamp(),\"".
        mysql_escape_string($responseBody)."\" )", $con);

        if (!$result)
            die(mysql_error($con));

        $lastretrievedtime = time();
        $tweets = json_decode($responseBody);
        
        EchoTweets($tweets, $lastretrievedtime);
    }
}
else {
    // Do not retrieve the tweets, just use the cached result.
    $tweets = json_decode($row['body']);
    EchoTweets($tweets, $lastretrievedtime);
}

?>
</div>
</body>
</html>