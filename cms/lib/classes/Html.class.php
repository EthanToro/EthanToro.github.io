<?php
class Html {

    /** Truncate HTML, close opened tags. UTF-8 aware, and aware of unpaired tags
     * (which don't need a matching closing tag)
     *
     * @param int $max_length Maximum length of the characters of the string
     * @param string $html
     * @param string $indicator Suffix to use if string was truncated.
     * @return string
     */
    static function close_tags($html){
        $single_tags = array('meta', 'img', 'br', 'link', 'area', 'input', 'hr', 'col', 'param', 'base');

        preg_match_all('~<([a-z0-9]+)(?: .*)?(?<![/|/ ])>~iU', $html, $result);
        $openedtags = $result[1];
        preg_match_all('~</([a-z0-9]+)>~iU', $html, $result);
        $closedtags = $result[1];
        $len_opened = count($openedtags);

        if (count($closedtags) == $len_opened)
        {
            return $html;
        }

        $openedtags = array_reverse($openedtags);

        for ($i = 0; $i < $len_opened; $i++)
        {
            if ( ! in_array($openedtags[$i], $single_tags))
            {
                if (($key = array_search($openedtags[$i], $closedtags)) !== FALSE)
                {
                    unset($closedtags[$key]);
                }
                else
                {
                    $html .= '</'.$openedtags[$i].'>';
                }
            }
        }

        return $html;
    }
}
