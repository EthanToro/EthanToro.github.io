<?php include('contactUs-mailer.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact Us -Corey Compressor, Inc.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
          content="Contact Corey Compressor, Inc. at (239) 693-3430 for Fort Myers location, (813) 381-3827 for Tampa Location, or (954) 457-4440 for Hallandale location."/>
    <?php include('cms/includes/styles.php'); ?>
</head>

<body>
<!-- google tag manager -->
<?php include 'google-tag-manager.php'; ?>

<!-- start content -->
<div class="container">
    <?php include('header_main.php'); ?>
    <div class="col-md-6 rule-right">
        <h1>Contact Us</h1>
        <div class="column_left">
            <div class="clear">
            </div>
            <div class="formbox">
                <?php @print $success; ?>
                <form role="form" action="#" method="post" id="contactForm">


                    <div class="form-group row">
                        <label class="control-label col-md-4"><span class="red">*</span> Topic:</label>
                        <div class="col-md-8">
                            <select name="interested" id="interested" required class="form-control">
                                <option SELECTED disabled="disabled" value="">Select a department</option>
                                <option value="Sales">Sales</option>
                                <option value="Service">Service</option>
                                <option value="Employment">Employment</option>
                                <option value="Accounting">Accounting</option>
                                <option value="Info">Information</option>
                                <option value="Parts">Parts</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="email" class="col-sm-4 form-control-label"><span class="red">*</span> Your
                            E-mail:</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span class="red">*</span> Your
                            Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span class="red">*</span> Company
                            Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="companyname" name="companyname" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span class="red">*</span> Phone:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control " id="phone" name="phone" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"> FAX:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control " id="fax" name="fax">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="comments" class="col-sm-12 form-control-label">Commments:</label>
                        <div class="col-sm-12">
                            <textarea id="comments" name="comments" class="form-control "></textarea>
                        </div>
                    </div>

                    <!-- recaptcha -->
                    <div class="control-group">
                        <label for="captchaImage" class="control-label"></label>
                        <div class="controls"><img id="captcha" src="securimage/securimage_show.php"
                                                   alt="CAPTCHA Image"/> Can't read this? <a href="#"
                                                                                             onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false">
                                <u>Try another</u></a></div>
                    </div>
                    <div class="control-group">
                        <label for="captchaText" class="control-label">Type the letters here:</label>
                        <div class="controls">
                            <input type="text" name="captcha_code" size="10" maxlength="6"/>
                            <?php @print $errCaptcha; ?>
                            <br>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4"><br>

                            <input type="submit" value="Submit" id="submit" name="submit" class="btn btn-primary">
                        </div>
                    </div>
                </form>
                <br/>
                <span class="red">*</span> Required fields
                <div align="center">
                </div>

            </div>
        </div>

    </div>
    <div class="col-md-6">
        <h1>&nbsp;</h1>
        <hr class="rule_blue">
        <div class="clear"></div>
        <div class="text-center">
            <img src="images/contact.jpg" alt="quote" class="img-responsive">
            <h3>Corey Compressor, Inc.</h3>
            <p>5778 Enterprise Parkway &bull; Fort Myers, Florida 33905</p>
            <p>Phone (239) 693-3430 FAX (239) 693-5826</p>
            <br>
            <a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;sll=37.0625,-95.677068&amp;sspn=51.887315,81.738281&amp;q=Corey+Compressor&amp;ie=UTF8&amp;om=1&amp;ll=26.679827,-81.830807&amp;spn=0.115498,0.159645"
               target="_blank">Get Directions on Google Maps</a>
            <a href="http://maps.google.com/maps?f=q&amp;hl=en&amp;sll=37.0625,-95.677068&amp;sspn=51.887315,81.738281&amp;q=Corey+Compressor&amp;ie=UTF8&amp;om=1&amp;ll=26.679827,-81.830807&amp;spn=0.115498,0.159645"
               target="_blank"><img src="images/google_maps.jpg" alt="get directions" class="img-responsive"></a>
        </div>
    </div>

    <div class="clear">
    </div>

    <!-- end content -->
</div>
<?php include('cms/includes/mainFooter.php'); ?>
</body>
</html>