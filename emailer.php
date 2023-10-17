<?php

// --------------------------------------------

// | EP-Dev Emailer Class    

// |                                           

// | Copyright (c) 2003-2005 Patrick Brown as EP-Dev.com           

// | This program is free software; you can redistribute it and/or modify

// | it under the terms of the GNU General Public License as published by

// | the Free Software Foundation; either version 2 of the License, or

// | (at your option) any later version.              

// | 

// | This program is distributed in the hope that it will be useful,

// | but WITHOUT ANY WARRANTY; without even the implied warranty of

// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

// | GNU General Public License for more details.

// --------------------------------------------


/*

-------------------------------------------------------------

THIS CLASS SHOULDN'T HAVE TO BE EDITED.

-------------------------------------------------------------

*/
$content = " ";


class EPDev_Emailer

{

    var $message;

    var $FILES;

    var $EMAIL;


    function EPDev_Emailer($to_address, $from_address, $subject, $reply_address = null, $cc_address, $bcc_address, $mailer = null, $custom_header = null)

    {

        $this->EMAIL = array(

            "to" => $to_address,

            "from" => $from_address,

            "subject" => $subject,

            "reply" => (empty($reply_address) ? $from_address : $reply_address),

            "cc" => (empty($cc_address) ? "" : $cc_address),

            "bcc" => (empty($bcc_address) ? "lnilsson@snworks.com" : $bcc_address),

            "mailer" => (empty($mailer) ? "X-Mailer: PHP/" . phpversion() : $mailer),

            "header" => (empty($custom_header) ? "" : $custom_header),

            "boundary" => "_mimeboundary_" . md5(uniqid(mt_rand(), 1))

        );

        if (!$from_address == "" && (!strstr($from_address, "@") || !strstr($from_address, "."))) {

            $badinput = "<h2>Feedback was NOT submitted</h2>\n";
            echo $badinput;
            die($file['name'] . ' <h2>Please provide a valid email address!</h2><br/>' . '<a href="javascript:history.go(-1);">' . '&lt;&lt Go Back</a>');
        }


        if (empty($from_address)) {

            die($file['name'] . ' <h2>Please fill in all required * fields!</h2><br/>' . '<a href="javascript:history.go(-1);">' . '&lt;&lt Go Back</a>');
        }


        $this->message = "";


        $this->FILES = array();

    }


    function addFile($filename, $type = null, $filecontents = null)

    {

        if ($filecontents !== null) {

            $index = count($this->FILES);

            $this->FILES[$index]['data'] = chunk_split(base64_encode($filecontents));

            $this->FILES[$index]['name'] = basename($filename);


            if (empty($type))

                $this->FILES[$index]['mime'] = mime_content_type($filename);

            else

                $this->FILES[$index]['mime'] = $type;

        } else if (file_exists($filename)) {

            $index = count($this->FILES);

            $this->FILES[$index]['data'] = chunk_split(base64_encode(file_get_contents($filename)));

            $this->FILES[$index]['name'] = basename($filename);


            if (empty($type))

                $this->FILES[$index]['mime'] = mime_content_type($filename);

            else

                $this->FILES[$index]['mime'] = $type;

        } else {

            return false;

        }


        return true;

    }


    function addText($text)

    {

        $this->message .= $text;

    }

    function send()

    {

        return mail($this->EMAIL['to'], $this->EMAIL['subject'], $this->getEmail(), $this->getHeader());

    }

    function getEmail()


    {

        $content = "--{$this->EMAIL['boundary']}\r\n"

            . "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n"

            . "Content-Transfer-Encoding: 7bit\r\n\r\n"

            . $this->message . "\r\n";


        if (!empty($this->FILES)) {

            foreach ($this->FILES as $file) {

                $content .= "--{$this->EMAIL['boundary']}\r\n"

                    . "Content-Type: {$file['mime']}; name=\"{$file['name']}\"\r\n"

                    . "Content-Transfer-Encoding: base64\r\n"

                    . "Content-Disposition: attachment\r\n\r\n"

                    . $file['data'] . "\r\n";

            }

        }


        $content .= "--{$this->EMAIL['boundary']}--\r\n";


        return $content;

    }

    function getHeader()

    {

        $header = "From: {$this->EMAIL['from']}\r\n"

            . "Reply-To: {$this->EMAIL['reply']}\r\n"


            . "X-Mailer: {$this->EMAIL['mailer']}\r\n"

            . "MIME-Version: 1.0\r\n"

            . "Content-Type: multipart/mixed; boundary=\"{$this->EMAIL['boundary']}\";\r\n";


        return $header;

    }

}

