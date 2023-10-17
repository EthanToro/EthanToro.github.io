<?php

if (session_id() == '') {
    session_start();
}


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
    $clientEmail = "info@coreycompressor.com";
//include the SecurImage Captcha
    include 'securimage/securimage.php';
    $securimage = new securimage();


    if ($_POST["interested"] == "Sales") {
        $clientEmail = "sales@coreycompressor.com";
        $location = "Sales";
    }

    if ($_POST["interested"] == "Service") {
        $clientEmail = "service@coreycompressor.com";
        $location = "Service";
    }

    if ($_POST["interested"] == "Employment") {
        $clientEmail = "employment@coreycompressor.com";
        $location = "Employment";
    }

    if ($_POST["interested"] == "Accounting") {
        $clientEmail = "accounting@coreycompressor.com";
//$clientEmail = "lnilsson@snworks.com";
        $location = "Accounting";
    }

    if ($_POST["interested"] == "Info") {
        $clientEmail = "info@coreycompressor.com";
        $location = "Info";
    }

    if ($_POST["interested"] == "Parts") {
        $clientEmail = "parts@coreycompressor.com";
//$clientEmail = "lnilsson@snworks.com";
        $location = "Parts";
    }


    //Check form Inputs


    $name = strip_tags(Post2('name'));

    $email = strip_tags(Post2('email'));
    $phone = strip_tags(Post2('phone'));
    $companyname = strip_tags(Post2('companyname'));
    $fax = strip_tags(Post2('fax'));
    $comments = strip_tags(Post2('comments'));

    $subject = "Contact Form from Corey Compressor website";
    $error = '';


    //form variables
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    }


    if (isset($_POST['email'])) {
        $email = $_POST['email'];
    }

    if (isset($_POST['phone'])) {
        $phone = $_POST['phone'];
    }


    if (isset($_POST['comments'])) {
        $comments = $_POST['comments'];
    }

    if (isset($_POST['location'])) {
        $comments = $_POST['location'];
    }

    if (isset($_POST['companyname'])) {
        $companyname = $_POST['companyname'];
    }


    if ($securimage->check($_POST['captcha_code']) == false) {
        $error = $errCaptcha = "<div class='alert alert-error' style='background-color:#ff0000;'>
									<button type='button' class='close' data-dismiss='alert'>x</button>
									Invalid Captcha, Try Again</div>";
    }


    if ($error == '') {
        //email message setup
        $message = "
		
		Department: $location
		E-mail: $email
		Your Name: $name
		Company Name: $companyname
		Phone: $phone
		FAX: $fax
		Comments: $comments
		";

        //send email
        $from = $email;
        $headers = "From:" . $from . "\r\nReplyTo:$email";
        @mail($clientEmail, $subject, $message, $headers);

        $success = "<div class='alert alert-success'>
		<button type='button' class='close' data-dismiss='alert'>x</button>
		Thank You! Your Form has been Submitted</div>";
    }
}
?>