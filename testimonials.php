<?php include('cms/includes/config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Testimonials - Corey Compressor, Inc.</title>
    <meta name="description" content="Read what our customers have to say...">
    <?php include('cms/includes/styles.php'); ?>
    <link href='https://fonts.googleapis.com/css?family=Alex+Brush' rel='stylesheet' type='text/css'>
</head>
<body>
<!-- Start content -->
<div class="container">
    <?php include('header_main.php'); ?>

    <div class="col-md-6 rule-right">
        <h1>TESTIMONIALS</h1>
        <hr class="rule_purple">
        <div class="clear"></div>
        <p>&nbsp;</p>

        <?php
        $feed = new Feed('Testimonials');
        $posts = $feed->getPosts(0, null, "title ASC");

        foreach ($posts as $p) {
            ?>
            <!-- Start Post -->
            <p><?php echo $p->getContent(); ?></p>
            <h3 class="text-right">
                &#8212; <?php echo $p->getTitle(); ?>
            </h3>
            <p>&nbsp;</p>
            <!-- End Post -->
            <?php
        }
        ?>
        <p class="text-center">
        <h3>We appreciate your feedback.</h3>
        Telephone: 239.693.3430<br>
        <br/> Fax: 239.693.5826
        <br/>
        <a href="contact_us.php">Contact us online</a>
        </p>
    </div>

    <div class="col-md-6">
        <h1>DID YOU KNOW</h1>
        <hr class="rule_blue">
        <p>Can't find the part you are looking for? <br/>
            That's not a problem for Corey Compressor; if it's not available, we will make it for you.</p>
        <p>Seen here, in the Bury manufacturing plant circa 1918, is the machining of a 36" cylinder on a vertical
            boring machine.</p>
        <p><img src="images/scan0011.jpg" alt="nitrogen systems" class="img-responsive text-center"></p>
        <p>The machine was driven by a leather flat belt, connected to a line shaft, which runs the length of the plant.
            The line shaft was driven by a steam engine on one end of the plant.</p>
    </div>
    <!-- End content -->
</div>

<?php include('cms/includes/mainFooter.php'); ?>
</body>
</html>
