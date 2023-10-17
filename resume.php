<?php
include 'cms/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Career opportunity - Corey Compressor, Inc.</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="description"
          content="Our Preventative maintenance programs provide complete, recorded and properly scheduled equipment inspections by trained service technicians."/>
    <?php include('cms/includes/styles.php'); ?>
</head>
<body>
<!-- google tag manager -->
<?php include 'google-tag-manager.php'; ?>
<!-- start content -->
<div class="container">
    <?php include('header_main.php'); ?>


    <div class="col-md-12">

        <h1>Apply for your career opportunity online</h1>
        <hr class="rule_purple"/>


        <?php @print $success; ?>
        <form id="comment_form" action="verify.php" method="post" enctype='multipart/form-data'
              class="form-horizontal pull-left">

            <div class="form-group">
                <label class="col-md-4 control-label"><span style="color: #F00">*</span> Select position</label>
                <div class="col-md-8">


                    <select name="position" id="position" required class="form-control">
                        <option selected="selected" disabled="disabled" value="">Select one</option>

                        <?php

                        $feed = new Feed('Employment'); //newsfeed is the feed name in the backend

                        $posts = $feed->getPosts(0, null, "userSort asc"); // with no arguments this function returns a array of all the posts
                        foreach ($posts as $p) {

                            ?>


                            <option value="<?php echo $p->getTitle(); ?>"><?php echo $p->getTitle(); ?></option>

                            <?php
                        }
                        ?>
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label for="name" class="col-md-4 control-label"><span style="color: #F00">*</span> Full Name: </label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="firstName" id="firstName" placeholder="Full Name"
                           required>
                </div>
            </div>
            <div class="form-group">
                <label for="phone" class="col-md-4 control-label"><span style="color: #F00">*</span> Phone: </label>
                <div class="col-md-8">
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label"><span style="color: #F00">*</span> Email: </label>
                <div class="col-md-8">
                    <input type="text" name="from" id="from" class="form-control" required placeholder="Email">
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label"><span style="color: #F00">*</span> Cover letter: </label>
                <div class="col-md-8">
                    <textarea name="notes" class="form-control" rows="4"
                              placeholder="Type or paste your cover letter here" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-md-4 control-label"><span style="color: #F00">*</span> Attach resume</label>
                <label class="col-md-5 control-label">

                    (ONLY txt, docx, pdf)<br>
                </label>
                <div class="col-md-3">
                    <input type='file' name='uploaded_file' required></div>
            </div>


            <div class="form-group">
                <label for="captchaImage" class="control-label col-md-4">Captcha Image:</label>
                <div class="col-md-3">
                    <img id="captcha" src="securimage/securimage_show.php" alt="CAPTCHA Image"/>
                    <br>
                    <a href="#"
                       onclick="document.getElementById('captcha').src = 'securimage/securimage_show.php?' + Math.random(); return false">[
                        Different Image ]</a>
                </div>
            </div>

            <div class="form-group">
                <label for="captchaText" class="control-label col-md-4"><span style="color: #F00">*</span> Captcha Text:</label>
                <div class="col-md-3">
                    <input class="form-control" type="text" name="captcha_code" size="10" maxlength="6"/>
                    <?php @print $errCaptcha; ?>

                </div>
            </div>


            <div class="col-md-4">
            </div>
            <div class="col-md-8">
                <br>
                <input type="submit" value="Submit" id="submit" name="submit" class="btn btn-primary"> <span
                        style="color: #F00">*</span> required fields
            </div>


        </form>

    </div>


    <div class="col-md-6 rule-left"></div>


    <!-- end content -->
</div>
<?php include('cms/includes/mainFooter.php'); ?>
</body>
</html>