<?php
// Include the configuration file to access necessary settings.
include 'cms/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>History - Corey Compressor, Inc.</title>
    <meta name="description"
          content="We offer excellent compensation, benefits, a vehicle, and an ideal working environment that is low-key and family-oriented."/>
    <?php
    // Include the styles for this page.
    include('cms/includes/styles.php');
    ?>
</head>
<body>
<!-- start content -->
<div class="container">
    <?php
    // Include the main header for the website.
    include('header_main.php');
    ?>
    <div class="col-md-6 rule-right-main">
        <h1>HISTORY</h1>
        <hr class="rule_purple"/>
        <div class="clear">
        </div>

        <?php
        // Create an instance of the Feed class and retrieve posts from the 'History' category, sorted by title.
        $feed = new Feed('History');
        $posts = $feed->getPosts(0, null, "title ASC");

        foreach ($posts as $p) {
            ?>
            <!-- start Post -->
            <p>
                <?php
                // Output the content of each post.
                echo $p->getContent();
                ?>
            </p>
            <!-- end Post -->
            <?php
        }
        ?>
    </div>
    <div class="col-md-6 rule-left-main">
        <h3>DID YOU KNOW</h3>
        <hr class="rule_blue"/>
        <div class="clear">
        </div>
        <p>Corey Compressor had its beginnings with the Bury Compressor Company, which was established in 1902.</p>
        <p>&nbsp;</p>
        <img src="images/good-compressor.jpg" alt="Corey Compressor" class="img-responsive text-center"/><br/>
        <br/>
    </div>
    <!-- end content -->
</div>
<?php
// Include the main footer for the website.
include('cms/includes/mainFooter.php');
?>
</body>
</html>
