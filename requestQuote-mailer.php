<?php

if (session_id() == '') {
    session_start();
}

//session_start();
//Error Variables
$errName = "";
$errEmail = "";
$errComments = "";

$success = "";

function Post2($k, $default = '')
{
    if (isset($_POST[$k])) {
        if (!empty($_POST[$k])) {
            return $_POST[$k];
        }
    }
    return $default;
}

if (Post2('submit', false)) {
    //Set Email recipient


    $clientEmail = "sales@coreycompressor.com";
    //$clientEmail = "lnilsson@snworks.com";
    include 'securimage/securimage.php';
    $securimage = new securimage();


    //Check form Inputs
    $name = strip_tags(Post2('name'));
    $email = strip_tags(Post2('email'));
    $phone = strip_tags(Post2('phone'));
    $companyname = strip_tags(Post2('companyname'));
    $fax = strip_tags(Post2('fax'));
    $comments = strip_tags(Post2('comments'));

    $subject = "Quote resquest Form from Corey Compressor website";
    $error = '';

    if ($name == "") {
        $error = $errName = "<div class='alert alert-error'>
									<button type='button' class='close' data-dismiss='alert'>x</button>
									Please enter a name</div>";
    }

    if (!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", $email)) {
        $error = $errEmail = "<div class='alert alert-error'>
									<button type='button' class='close' data-dismiss='alert'>x</button>
									Please enter a Valid Email</div>";
    }

    if ($securimage->check($_POST['captcha_code']) == false) {
        $error = $errCaptcha = "<div class='alert alert-error' style='background-color:#ff0000;'>
									<button type='button' class='close' data-dismiss='alert'>x</button>
									Invalid Captcha, Try Again</div>";
    }


    if ($error == '') {
        //email message setup
        $message = "
		E-mail: $email
		Your Name: $name
		Company Name: $companyname
		Phone: $phone
		FAX: $fax
		Which product would you like a quote on? $comments
		";

        //send email
        $from = $email;
        $headers = "From: " . $from . "\r\nReplyTo:$email";
        @mail($clientEmail, $subject, $message, $headers);

        $success = "<div class='alert alert-success'>
		<button type='button' class='close' data-dismiss='alert'>x</button>
		Thank You! Your Form has been Submitted</div>";
    }
}
?>