<?php

class Twitter{
    private $username;
    private $password;

    public function __construct() {
        $sql = "SELECT * FROM settings WHERE name='twitterName'";
        if (($result = $database->getRows($sql)) === false) die(mysql_error() . "\nCouldn't construct twitter object.");

        if (count($result) > 0)
            $this->username = $result[0]['value'];
    }

    public function getTweets($number=1) {
        $tweets = json_decode(file_get_contents("http://api.twitter.com/1/statuses/user_timeline.json?screen_name={$this->username}&count={$number}"));

        $t = array();
        $i = 0;

        foreach ($tweets as $t) {
            $t[$i]['id']    = $t->id_str;
            $t[$i]['user']  = $t->user->name;
            $t[$i]['text']  = $t->text;
            $t[$i]['time']  = twitter_time($t->created_at);
            $t[$i]['url'] 	= "http://twitter.com/$username/status/".$t->id_str;
            $i++
        }

        return $t;
    }

    public function makeTweet($tweet) {
        // I wanna hug. ;n;
        // to do
    }

    public function setName($name) {
        $sql = "UPDATE settings value='$name' WHERE name='twitterName'";
        if ($database->query($sql) === false) die(mysql_error() . "\nCouldn't set username for twitter.");

        $this->username = $name;
    }

    public function setPassword($pass) {
    }
}
