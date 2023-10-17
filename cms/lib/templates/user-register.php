<?php

// CAPTCHA CODE ////////////////////////////////////////////////////////
    $url = 'http://api.textcaptcha.com/4yhq291xuggs0wcko40gsg8wc34aggkb';
    try {
        $xml = @new SimpleXMLElement($url,null,true);
    } catch (Exception $e) {
        $fallback = '<captcha>' .
            '<question>Is ice hot or cole?</question>'.
            '<answer>'.md5('cold').'</answer></captcha>';
        $xml = new SimpleXMLElement($fallback);
    }

    $question = (string) $xml->question;
    $ans = array();
    $salt = 'Ah, The Scalene Triangle';
    foreach ($xml->answer as $hash)
        $ans[] = (string) md5($hash.$salt);
// CAPTCH'n END ////////////////////////////////////////////////////////

$formUrl = '/cms/lib/actions/user-register.php';
?>

<form action="<?php echo $formUrl;?>" method="post">
    <?php if (isset($errors) && isset($errors['username'])) { ?>
        <p class="error"><?php echo $errors['username']; ?></p>
    <?php } ?>
    <label for="username"><span class="required">*</span>Username:</label>
    <br/><input id="username" type="text" name="username" placeholder="username" <?php if (Post('username')) echo 'value="'.Post('username').'"';?>/>

    <br/><br/>
    <?php if (isset($errors) && isset($errors['email'])) { ?>
        <p class="error"><?php echo $errors['email']; ?></p>
    <?php } ?>
    <label for="email"><span class="required">*</span>Email:</label>
    <br/><input id="email" type="email" name="email" placeholder="email" <?php if (Post('email')) echo 'value="'.Post('email').'"';?>/>


    <br/><br/>
    <?php if (isset($errors) && isset($errors['password'])) { ?>
        <p class="error"><?php echo $errors['password']; ?></p>
    <?php } ?>
    <label for="password1"><span class="required">*</span>Password:</label>
    <br/><input id="password1" type="text" name="password1" placeholder="password" <?php if (Post('password1')) echo 'value="'.Post('password1').'"';?>/>
    <br/><label for="password2"><span class="required">*</span>Confirm Password:</label>
    <br/><input id="password2" type="text" name="password2" placeholder="confirm password" <?php if (Post('password2')) echo 'value="'.Post('password2').'"';?>/>

    <br/><br/>
    <?php if (isset($errors) && isset($errors['captcha'])) { ?>
        <p class="error"><?php echo $errors['captcha']; ?></p>
    <?php } ?>
    <label for="captcha"><span class="required">*</span>Captcha:</label>
    <br/><small class="left"><?php echo $question; ?></small>
    <br/><input type="text" name="captcha" id="captcha" placeholder="Captcha Answer" />

    <input type="hidden" name="answers[]" value="<?php echo $a; ?>" />

    <br/><br/>
    <input type="submit" name="submit" value="Submit" />
</form>
