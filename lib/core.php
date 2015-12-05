<?php
define('USERNAME', 'root'); // the username for the SQL server.
define('ALLOWED_TAGS', '<b><i><u>'); // The tags that are allowed in blog posts & comments.

// The list of allowed tags that gets displayed to the user.
define('ALLOWED_TAGS_HELP', '&lt;b&gt;, &lt;i&gt;, &lt;u&gt;.');

// The entries per page for pagination.
define('ENTRIES_PER_PAGE', 5);

// URI that returns the specified number of most recent statuses posted by
// the user with the specified screen name, as JSON.
// *** Should be requested no more often than 60 seconds! ***
DEFINE('TWITTER_GET_STATUSES_JSON',
        'http://api.twitter.com/1/statuses/user_timeline.json?screen_name=benblamey&count=20');

DEFINE('TWITTER_CONNECT_TIMEOUT_SECONDS',5); // Timeout for obtaining connection.
DEFINE('TWITTER_GET_TIMEOUT_SECONDS',15); // Timeout for the whole GET request.

// The minimum permitted interval between API requests (in seconds).
DEFINE('TWITTER_MINIMUM_REQUEST_INTERVAL', 60);

function Initialize() {

    // Connect to SQL server (null indicates we want to connect to localhost).
    $con = mysql_connect(null, USERNAME);
    if (!$con)
        die("Connection failed: " . mysql_error());

    // To keep things simple for this demo, we automatically create the database
    // and populate it with all the necessary tables.

    // Create the new database, if neccessary.
    if (!mysql_select_db("blog", $con)) {
        $result = mysql_query("CREATE DATABASE blog", $con);
        
        $result = mysql_select_db("blog", $con);
        if (!$result) 
            die(mysql_error($con));
        
        // Populate the database with tables.

        // This table holds the blog entries themselves.
        $result = mysql_query("CREATE TABLE entries
                    (
                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                    date DATETIME NOT NULL,
                    title VARCHAR(1024) NOT NULL,
                    author VARCHAR(255) NOT NULL,
                    body TEXT(3000) NOT NULL
                    )", $con);
        if (!$result)
            die(mysql_error($con));

        // This table holds the blog entry comments.
        $result = mysql_query("CREATE TABLE comments
                    (
                    entryid INT NOT NULL,
                    date DATETIME NOT NULL,
                    author VARCHAR(255) NOT NULL,
                    body TEXT(1000) NOT NULL
                    )", $con);
        if (!$result)
            die(mysql_error($con));

        // The table acts as a cache for the tweets.
        $result = mysql_query("CREATE TABLE tweets
                    (
                    date DATETIME NOT NULL,
                    body TEXT(3000)
                    )", $con);
        if (!$result)
            die(mysql_error($con));
    }

    return $con;
}

// Destroys the 'blog' database.
function Nuke() {
    $con = mysql_connect(null, USERNAME);
    mysql_query("DROP DATABASE blog");
    return null;
    }

// Echos pagination links.
function EchoPaginationLinks($pagecount, $currentpageindex) {
    if ($pagecount > 1) {
        for ($pageindex = 1; $pageindex <= $pagecount; $pageindex++) {
            if ($pageindex == $currentpageindex) {
                echo(' ' . $pageindex . "\n");
            } else {
                echo(" <a href='.?page=" . $pageindex . "'>" . $pageindex . "</a> \n");
            }
        }
    }
}

// Echos a set of tweets retrieved from the twitter API.
function EchoTweets($tweets, $timestamp)
{
    // Sometimes we timeout before getting all the JSON, so we need to check that
    // we actually have an array.
    if (is_array($tweets))
    {
        // Print out the tweets.
        foreach ($tweets as $tweet)
        {
            echo("<p>\"$tweet->text\" - <i>at $tweet->created_at</i></p>\n");
        }

        $timestampstring = gmdate(DATE_RFC850, $timestamp);
        echo("<p style=\"margin-top:50px;\">Last Updated: $timestampstring</p>");
    }
    else {
        echo("Data from twitter server wasn't properly decoded.");
    }

}


?>
