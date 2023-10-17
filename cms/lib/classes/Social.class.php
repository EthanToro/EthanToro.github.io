<?php
/* 
 * Social.class.php
 * Social Class for the initialization of social stuff
 *
 * function:        * returns:      *
 *  getTwitter()    * Twitter obj   *
 *  getFacebook()   * Facebook obj  *
 *  getLinkedIn()   * LinkedIn obj  *
 *  getTumblr()     * Tumblr obj    *
 *
 */

class Social {


    public function __construct() {
    }

    public function getTwitter() {
        return new Twitter();
    }

    public function getFacebook() {
    }

    public function getLinkedIn() {
    }

    public function getStoned() {
        return 'awwww yeah, I\'m so high right now.';
    }

    public function getTumblr() {
    }

}
