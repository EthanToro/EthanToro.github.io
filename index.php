<?php ob_start('ob_gzhandler'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Welcome to Corey Compressor, Inc.</title>
    <meta charset="UTF-8">
    <meta name='description'
          content='Welcome to Corey Compressor, Inc. We are a family company with over 155 years combined experience. We are also an authorized distributor of Atlas Copco.'/>
    <?php include('cms/includes/styles.php'); // Including stylesheets ?>
</head>

<body>
<?php include 'google-tag-manager.php'; // Include Google Tag Manager code ?>

<div class="container">
    <?php include('header_main.php'); // Include the main header ?>

    <div id="slideShow_container">
        <div id="slideshow">
            <div class="active">
                <a href="request_quote.php" title="Request a Quote 24/7"><img src="images/image2.jpg"
                                                                              alt="Slideshow Image 2"
                                                                              title="Request a Quote 24/7"
                                                                              class="img-responsive"/></a>
            </div>
            <div>
                <a href="request_quote.php" title="Request a Quote 24/7"><img src="images/image3.jpg"
                                                                              alt="Request a Quote"
                                                                              title="Request a Quote 24/7"
                                                                              class="img-responsive"/></a>
            </div>
            <div>
                <a href="request_quote.php" title="Request a Quote 24/7"><img src="images/image4.jpg"
                                                                              alt="Request a Quote"
                                                                              title="Request a Quote 24/7"
                                                                              class="img-responsive"/></a>
            </div>
            <div>
                <a href="request_quote.php" title="Request a Quote 24/7"><img src="images/image5.jpg"
                                                                              alt="Request a Quote"
                                                                              title="Request a Quote 24/7"
                                                                              class="img-responsive"/></a>
            </div>
            <div>
                <a href="request_quote.php" title="Request a Quote 24/7"><img src="images/image6.jpg"
                                                                              alt="Request a Quote"
                                                                              title="Request a Quote 24/7"
                                                                              class="img-responsive"/></a>
            </div>
            <!-- Other slideshow images -->
        </div>
    </div>

    <div>
        <div class="col-md-6 rule-right">
            <h1>WELCOME TO COREY COMPRESSOR, INC.</h1>
            <hr class="rule_red"/>
            <p>Corey Compressor is a family company with over 155 years combined experience in the air compressor
                industry. Through skill and honesty, we've acquired a reputation second to none in the South Florida
                region.</p>
            <p>From one generation to the next, we've enhanced our vast technical skills and knowledge. Coupling that
                knowledge with state-of-the-art equipment and the best technicians in the Southeastern United States, we
                have won the confidence of our customers and gained the respect of our fellow competitors. <a
                        href="testimonials.php" title="testimonials"><strong>Read what our customers are saying</strong><span
                            class="glyphicon glyphicon-chevron-right"></span></a></p>
            <p><i>Browse our entire website to learn more.</i></p>
        </div>

        <div class="col-md-6 rule-left">
            <h3>SERVICES</h3>
            <hr class="rule_purple"/>
            <ul class="lineHeight">
                <li><a href="services_Emergency_Service.php" title="24 Hour Service Support">24 Hour Service Support</a>
                </li>
                <!-- Other service items -->
            </ul>
        </div>
    </div>

    <?php include('cms/includes/logos.php'); // Include logos section ?>
</div>

<?php include('cms/includes/mainFooter.php'); // Include the main footer section ?>
</body>
</html>
