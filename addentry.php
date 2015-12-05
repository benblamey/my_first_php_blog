<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php include 'lib/core.php' ?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Add An Entry</title>
    </head>
    <body>
        <?php include 'header.php'; ?>

        <form action="entryadded.php" method="POST">
            Title<br />
            <input type="text" name="title"  /><br />
            Author<br />
            <input type="text" name="author" /><br />
            <textarea rows="20" cols="80" name="body"></textarea><br />
            <p>The following tags are allowed in the body:
                <b><?php echo(ALLOWED_TAGS_HELP); ?></b></p>
            <input type="submit" />
        </form>

        <br/>
        <br/>
        <br/>

        <!-- For debugging/demonstration purposes only.-->
        <form action="addmanyentries.php" method="POST">
            <input type="submit"
                   value="Test: Add lots of dummy entries" />
        </form>


    </body>
</html>
