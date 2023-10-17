<?php
// Including the configuration file for your CMS
include 'cms/includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employment opportunities - Corey Compressor, Inc.</title>
    <meta name="description"
          content="We offer excellent compensation, benefits, vehicle, and an ideal working environment that is low-key and family-oriented.">
    <?php
    // Including the external styles for your page
    include 'cms/includes/styles.php';
    ?>
</head>
<body>
<div class="container">
    <?php
    // Including the main header for your website
    include 'header_main.php';
    ?>
    <div class="col-md-6 rule-right-main">
        <h1>EMPLOYMENT OPPORTUNITIES</h1>
        <hr class="rule_purple">
        <div class="clear"></div>
        <p>We offer excellent compensation, benefits, vehicle, and an ideal working environment that is low-key and
            family-oriented.</p>

        <p>Combine all those things with our beautiful weather here in South Florida, and you have the perfect job! Why
            consider the competition when you can go with the best!</p>
        <h3>VIEW OUR JOB OPENINGS:</h3>

        <?php
        // Fetching and displaying job openings from a feed
        $feed = new Feed('Employment');
        $posts = $feed->getPosts(0, null, "userSort asc");
        foreach ($posts as $p) {
            ?>
            <div class="accordion text-left">
                <div class="accordion-section">
                    <a class="accordion-section-title"
                       href="#accordion<?php echo $p->getuserSort(); ?>"><?php echo $p->getTitle(); ?></a>
                    <div id="accordion<?php echo $p->getuserSort(); ?>" class="accordion-section-content">
                        <?php echo $p->getContent(); ?>
                        <h4><a href="resume.php"><u>Apply here <span
                                            class="glyphicon glyphicon-chevron-right"></span></u></a></h4>
                    </div>
                </div>
            </div>
            <br>
            <?php
        }
        ?>
        <p>Telephone: 239.693.3430</p>
        <p>Fax: 239.693.5826</p>
    </div>
    <div class="col-md-6 rule-left-main">
        <h3>DID YOU KNOW</h3>
        <hr class="rule_blue">
        <div class="clear"></div>
        <p>Corey Compressor had its beginnings with the Bury Compressor Company, which was established in 1902.</p>
        <p>&nbsp;</p>
        <img src="images/good-compressor.jpg" alt="Corey Compressor" class="img-responsive text-center"><br><br>
    </div>
</div>
<?php
// Including the main footer for your website
include 'cms/includes/mainFooter.php';
?>
</body>
</html>
