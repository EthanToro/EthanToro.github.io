<?php


include("emailer.php");


if ($_SERVER['REQUEST_METHOD'] == "POST") {


    $email;
    $captcha;
    $firstName;
    $phone;
    $from;
    $notes;
    $tmp_name;
    $size;
    $name;
    $data;
    $file;
    $errCaptcha = "";
    $success = "";


    function Post($k, $default = '')
    {
        if (isset($_POST[$k])) {
            if (!empty($_POST[$k])) {
                return $_POST[$k];
            }
        }
        return $default;
    }

    if (Post('submit', false)) {


//include the SecurImage Captcha
        include 'securimage/securimage.php';
        $securimage = new securimage();

        if (isset($_POST['email'])) {
            $email = $_POST['email'];
        }


        if (isset($_POST['firstName'])) {
            $firstName = $_POST['firstName'];
        }


        if (isset($_POST['phone'])) {
            $areaCode = $_POST['phone'];
        }


        if (isset($_POST['from'])) {
            $from = $_POST['from'];
        }


        if (isset($_POST['notes'])) {
            $notes = $_POST['notes'];
        }
        if (isset($_POST['tmp_name'])) {
            $tmp_name = $_POST['tmp_name'];
        }
        if (isset($_POST['size'])) {
            $size = $_POST['size'];
        }
        if (isset($_POST['name'])) {
            $name = $_POST['name'];
        }
        if (isset($_POST['data'])) {
            $data = $_POST['data'];
        }
        if (isset($_POST['file'])) {
            $file = $_POST['file'];
        }


        if ($securimage->check($_POST['captcha_code']) == false) {
            die('Wrong captcha text!<br/>' .
                '<a href="javascript:history.go(-1);">' .
                '&lt;&lt Go Back</a>');
        }

        if ($error == '') {


            // your email
            $recipient = "employment@coreycompressor.com";
//$recipient = "lnilsson@snworks.com";

// person sending it

            $from = "$from";


// subject

            $subject = "Job Applicant - Corey Compressor";


            // email message

            $message = "

Job Applicant (resume attached)

Position applying for: $position

Name: $firstName

Phone: $phone

Email: $from

Cover letter: $notes


";


// where to move file to once uploaded

            $uploadedFolder = "uploads/";


            // initialize email object (to, from, subject)

            $myEmail = @new EPDev_Emailer($recipient, $from, $subject);


// Add the message to the email

            $myEmail->addText($message);


            $allowedExtensions = array("txt", "docx", "pdf");
            foreach ($_FILES as $file) {
                if ($file['tmp_name'] > '') {
                    if (!in_array(end(explode(".",
                        strtolower($file['name']))),
                        $allowedExtensions)) {
                        die($file['name'] . ' is an invalid file type!<br/>' .
                            '<a href="javascript:history.go(-1);">' .
                            '&lt;&lt Go Back</a>');
                    }
                }
            }

            if (!empty($_FILES['uploaded_file']['name'])) {

                // move the file from tmp to a folder

                move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $uploadedFolder . basename($_FILES['uploaded_file']['name']));


                // add as file attachment to email message

                $result = $myEmail->addFile($uploadedFolder . basename($_FILES['uploaded_file']['name']), $_FILES['uploaded_file']['type']);


                if (!$result)

                    die("Problem adding file to email!");

            }


            // actually send out the email


            $myEmail->send();

            header("Location: thank_you.php");
        }
    }
}
?>