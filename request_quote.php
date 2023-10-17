<?php include('requestQuote-mailer.php'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Request a quote -Corey Compressor, Inc.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
          content="Please fill out the form to request a quote on any products from Corey Compressor, Inc. We are more than happy to assist you."/>
    <?php include('cms/includes/styles.php'); ?>
</head>

<body>
<!-- google tag manager -->
<?php include 'google-tag-manager.php'; ?>

<!-- start content -->
<div class="container">
    <?php include('header_main.php'); ?>
    <div class="col-md-6 rule-right">
        <h1>REQUEST A QUOTE</h1>
        <div class="column_left">
            <div class="clear">
            </div>
            <div class="formbox">
                <?php @print $success; ?>
                <form role="form" action="#" method="post" id="requestForm">
                    <div class="form-group row">
                        <label for="email" class="col-sm-4 form-control-label"><span style="color: #F00">*</span> Your
                            E-mail:</label>
                        <div class="col-sm-8">
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span style="color: #F00">*</span> Your
                            Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span style="color: #F00">*</span> Company
                            Name:</label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="companyname" name="companyname" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-sm-4 form-control-label"><span style="color: #F00">*</span> Phone:</label>
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
                        <label for="comments" class="col-sm-12 form-control-label">Which product would you like a quote
                            on?</label>
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
                <span style="color: #F00">*</span> Required fields
                <div align="center">
                </div>
                <div class="row">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <hr class="rule-blue">
        <figure class="text-center">
            <img src="images/quote.jpg" alt="quote" class="img-responsive">
        </figure>
    </div>
    <?php include('cms/includes/logos.php'); ?>

    <!-- end content -->
</div>
<?php include('cms/includes/mainFooter.php'); ?>
</body>
</html>