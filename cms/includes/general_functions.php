<?php
function Post($key, $def = null)
{
    return (isset($_POST[$key]) && !empty($_POST[$key]) ? $_POST[$key] : $def);
}

function Get($key, $def = null)
{
    return (isset($_GET[$key]) && !empty($_GET[$key]) ? $_GET[$key] : $def);
}

function Request($key, $def = null)
{
    return (isset($_REQUEST[$key]) && !empty($_REQUEST[$key]) ? $_REQUEST[$key] : $def);
}

function CheckLogin($invert = false)
{
    if ($invert) {
        header("Location: index.php");
    } else {
        if (!isset($_SESSION['user_obj'])) {
            header("Location: login.php");
        }
    }
}

function getUser()
{
    if (isset($_SESSION['user_obj'])) {
        return $_SESSION['user_obj'];
    } else {
        return false;
    }
}

function kickToCurb()
{
    header("Location:index.php");
    die();
}

function startsWith($Haystack, $Needle)
{
    return strpos($Haystack, $Needle) === 0;
}

function logout()
{
    $_SESSION = array();
    session_unset();
    session_destroy();
    session_start();
}

function isnull($args)
{
    for ($i = 0; $i < count($args); $i++) {
        if (is_null($args[$i])) {
            return true;
        }
    }
    return false;
}

function isnullWarnings($args, $errorMessages, &$is_null_error)
{
    if (count($args) != count($errorMessages)) die("isnullWarnings: error argument arrays count does not match!");
    for ($i = 0; $i < count($args); $i++) {
        if (is_null($args[$i])) {
            $is_null_error[] = $errorMessages[$i];

        }
    }
    if (count($is_null_error) > 0) return true;
    return false;
}

function CleanPostVars()
{
    $_POST = mres($_POST);
}

function CleanGetVars()
{
    $_GET = mres($_GET);
}

function CleanRequestVars()
{
    $_GET = mres($_GET);
    $_POST = mres($_POST);
}

function fixIt($s)
{
    $replace = array('\&quot;' => "", '\r' => "\r", '\n' => "\n", '\'' => "'", '\"' => '"', '"\r\n"' => "\r\n");
    return str_replace(array_keys($replace), array_values($replace), $s);
}

function mres($q)
{
    if (is_array($q)) foreach ($q as $k => $v) $q[$k] = mres($v); elseif (is_string($q)) $q = mysql_real_escape_string($q);
    return $q;
}

function getIpAddress()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function do_dump(&$var, $var_name = NULL, $indent = NULL, $reference = NULL)
{
    $do_dump_indent = "<span style='color:#666666;'>|</span> &nbsp;&nbsp; ";
    $reference = $reference . $var_name;
    $keyvar = 'the_do_dump_recursion_protection_scheme';
    $keyname = 'referenced_object_name';

    // So this is always visible and always left justified and readable
    echo "<div style='text-align:left; background-color:white; font: 100% monospace; color:black;'>";

    if (is_array($var) && isset($var[$keyvar])) {
        $real_var = &$var[$keyvar];
        $real_name = &$var[$keyname];
        $type = ucfirst(gettype($real_var));
        echo "$indent$var_name <span style='color:#666666'>$type</span> = <span style='color:#e87800;'>&amp;$real_name</span><br>";
    } else {
        $var = array($keyvar => $var, $keyname => $reference);
        $avar = &$var[$keyvar];

        $type = ucfirst(gettype($avar));
        if ($type == "String") $type_color = "<span style='color:green'>"; elseif ($type == "Integer") $type_color = "<span style='color:red'>";
        elseif ($type == "Double") {
            $type_color = "<span style='color:#0099c5'>";
            $type = "Float";
        } elseif ($type == "Boolean") $type_color = "<span style='color:#92008d'>";
        elseif ($type == "NULL") $type_color = "<span style='color:black'>";

        if (is_array($avar)) {
            $count = count($avar);
            echo "$indent" . ($var_name ? "$var_name => " : "") . "<span style='color:#666666'>$type ($count)</span><br>$indent(<br>";
            $keys = array_keys($avar);
            foreach ($keys as $name) {
                $value = &$avar[$name];
                do_dump($value, "['$name']", $indent . $do_dump_indent, $reference);
            }
            echo "$indent)<br>";
        } elseif (is_object($avar)) {
            echo "$indent$var_name <span style='color:#666666'>$type</span><br>$indent(<br>";
            foreach ($avar as $name => $value) do_dump($value, "$name", $indent . $do_dump_indent, $reference);
            echo "$indent)<br>";
        } elseif (is_int($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
        elseif (is_string($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color\"" . htmlentities($avar) . "\"</span><br>";
        elseif (is_float($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . htmlentities($avar) . "</span><br>";
        elseif (is_bool($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> $type_color" . ($avar == 1 ? "TRUE" : "FALSE") . "</span><br>";
        elseif (is_null($avar)) echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> {$type_color}NULL</span><br>";
        else echo "$indent$var_name = <span style='color:#666666'>$type(" . strlen($avar) . ")</span> " . htmlentities($avar) . "<br>";

        $var = $var[$keyvar];
    }

    echo "</div>";
    return $var;
}

function truncate($string, $limit, $break = ".", $pad = "...")
{
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit) return $string;
    $string = strip_tags($string, '<br>');

    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

function howLongAgo($a, $unix = false)
{
    $b = strtotime("now");
    if ($unix) {
        $c = $a;
    } else {
        $c = strtotime($a);
    }
    $d = $b - $c;
    $minute = 60;
    $hour = $minute * 60;
    $day = $hour * 24;
    $week = $day * 7;
    if (is_numeric($d) && $d > 0) {
        if ($d < 3) return "right now";
        if ($d < $minute) return floor($d) . " seconds ago";
        if ($d < $minute * 2) return "about 1 minute ago";
        if ($d < $hour) return floor($d / $minute) . " minutes ago";
        if ($d < $hour * 2) return "about 1 hour ago";
        if ($d < $day) return floor($d / $hour) . " hours ago";
        if ($d > $day && $d < $day * 2) return "yesterday";
        if ($d < $day * 365) return floor($d / $day) . " days ago";
        return "over a year ago";
    }
}

?>
