<?php
/**
 * $Id: String.lib.php 230 2017-03-28 08:58:16Z radu $
 *
 * String library.
 *
 * @author     Radu Maier <radu@webserv.co>
 * @copyright  Copyright (c) 2008 - 2014, Radu Maier
 * @license    http://www.opensource.org/licenses/bsd-license.php
 * @package    WEb
 * @subpackage WebString
 * @version    $Revision: 230 $
 * @since      Version 1
 * @access     public
 * @filesource
 */

/**
 * Security check.
 */
if (! defined('_WEB')) {
    /**
     * Stop script execution.
     */
    exit();
}

/**
 * String
 * String library.
 *
 * @author Radu Maier <radu@webserv.co>
 * @package WEb
 * @subpackage WebString
 */
class StringLibrary extends Library
{

    /**
     * Implement GetInstance() method.
     * This is required for PHP < 5.3 compatibility (no late static binding)
     *
     * @param string $className
     *            class name to instantiate
     * @return object
     */
    public static function GetInstance($className = __CLASS__)
    {
        
        /**
         * Call parent method with current class name as parameter.
         */
        return parent::_GetInstance($className);
    }

    /**
     * Check if haystack starts with needle.
     *
     * @param string $needle            
     * @param string $haystack            
     * @param bool $ignoreCase            
     *
     * @return bool
     */
    public function StartsWith($needle, $haystack, $ignoreCase = TRUE)
    {
        if (FALSE !== $ignoreCase) {
            $f_strpos = (function_exists('mb_stripos')) ? 'mb_stripos' : 'stripos';
        } else {
            $f_strpos = (function_exists('mb_strpos')) ? 'mb_strpos' : 'strpos';
        }
        
        return 0 === $f_strpos($haystack, $needle);
    }

    /**
     * Check if haystack ends with needle.
     *
     * @param string $needle            
     * @param string $haystack            
     *
     * @return bool
     */
    public function EndsWith($needle, $haystack)
    {
        
        /**
         * Check if multybyte function is available.
         *
         * @var string
         */
        $f_substr = (function_exists('mb_substr')) ? 'mb_substr' : 'substr';
        
        /**
         * Check if multybyte function is available.
         *
         * @var string
         */
        $f_strlen = (function_exists('mb_strlen')) ? 'mb_strlen' : 'strlen';
        
        /**
         *
         * @var string
         */
        $check = $f_substr($haystack, $f_strlen($haystack) - ($f_strlen($needle)));
        
        return $check == $needle;
    }

    /**
     * Check if haystack contains with needle.
     *
     * @param string $needle            
     * @param string $haystack            
     * @param bool $ignoreCase            
     *
     * @return bool
     */
    public function Contains($needle, $haystack, $ignoreCase = TRUE)
    {
        if (FALSE !== $ignoreCase) {
            $f_strpos = (function_exists('mb_stripos')) ? 'mb_stripos' : 'stripos';
        } else {
            $f_strpos = (function_exists('mb_strpos')) ? 'mb_strpos' : 'strpos';
        }
        
        return FALSE !== $f_strpos($haystack, $needle);
    }

    /**
     * Get first item of a string.
     *
     * @param string $string            
     * @param string $separator            
     *
     * @return string
     */
    public function GetFirstItem($string, $separator = ' ')
    {
        if (FALSE !== ($pos = strpos($string, $separator))) {
            return substr($string, 0, $pos);
        } else {
            return $string;
        }
    }

    /**
     * Remove known bad characters from a string.
     *
     * @param string $string
     *            input string
     * @return string
     */
    public function RemoveBadChars($string)
    {
        
        /**
         * Known bad characters.
         *
         * @var array
         */
        $bad = array(
            "`",
            "'",
            '"',
            "\b",
            "\n",
            "\r",
            "\t",
            "?",
            "!",
            "@",
            "~",
            "#",
            "%",
            "^",
            "&",
            "*",
            "+",
            "=",
            "[",
            "]",
            ":",
            ";",
            ",",
            "|",
            "\\",
            "{",
            "}",
            "(",
            ")",
            "\$"
        );
        
        /**
         * Remove bad chars.
         */
        $string = str_replace($bad, '', $string);
        
        /**
         * Return result.
         */
        return $string;
    }

    /**
     * Remove known bad characters from a request (_GET) string.
     *
     * @param string $string
     *            input string
     * @return string
     */
    public function RemoveRequestBadChars($string)
    {
        
        /**
         * Known bad characters.
         *
         * @var array
         */
        $bad = array(
            "`",
            "'",
            '"',
            "\b",
            "\n",
            "\r",
            "\t",
            "?",
            "!",
            "~",
            "#",
            "^",
            "&",
            "*",
            "=",
            "[",
            "]",
            ":",
            ";",
            ",",
            "|",
            "\\",
            "{",
            "}",
            "(",
            ")",
            "\$"
        );
        
        /**
         * Remove bad chars.
         */
        $string = str_replace($bad, '', $string);
        
        /**
         * Return result.
         */
        return $string;
    }

    /**
     * Remove multiple white-spaces, tabs and new-lines
     *
     * @param string $string            
     * @return string
     */
    public function RemoveExtraSpaces($string)
    {
        return trim(str_replace('&#13;', '', preg_replace('/\s+/S', " ", $string)));
    }

    /**
     * Remove known bad characters from a string.
     *
     * @param string $string
     *            input string
     * @return string
     */
    public function RemoveNewlines($string)
    {
        
        /**
         * Newline chars.
         *
         * @var array
         */
        $nl = array(
            "\n",
            "\r"
        );
        
        /**
         * Remove bad chars.
         */
        $string = str_replace($nl, '', $string);
        
        /**
         * Return result.
         */
        return $string;
    }

    /**
     * Get the youtube video id.
     * Should work with link, embed, etc.
     *
     * @param
     *            string string
     * @return string
     */
    public function GetYoutubeVideoId($string)
    {
        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $string, $match)) {
            return $match[1];
        }
        
        return NULL;
    }

    /**
     * Strip tags from string.
     *
     * @param string $string            
     *
     * @return string
     */
    public function StripTags($string)
    {
        
        /**
         * Return result.
         */
        return preg_replace('/<(.|\s)*?\>/', '', $string);
    }

    /**
     * Disable tags from a string.
     *
     * @param string $string
     *            input string
     * @return string
     */
    public function DisableTags($string)
    {
        
        /**
         * Block <.
         *
         * @var string
         */
        $string = str_replace('&lt;', '&#60;', $string);
        
        /**
         * Block >.
         *
         * @var string
         */
        $string = str_replace('&gt;', '&#62;', $string);
        
        /**
         * Block <.
         *
         * @var string
         */
        $string = str_replace('<', '&#60;', $string);
        
        /**
         * Block >.
         *
         * @var string
         */
        $string = str_replace('>', '&#62;', $string);
        
        /**
         * Block <.
         *
         * @var string
         */
        $string = str_replace('%3C', '&#60;', $string);
        
        /**
         * Block >.
         *
         * @var string
         */
        $string = str_replace('%3E', '&#62;', $string);
        
        /**
         * Return result.
         */
        return $string;
    }

    /**
     * Extract number from a string.
     *
     * @param string $string            
     * @param string $separator            
     * @return string
     */
    public function ExtractNumber($string, $separator = '')
    {
        preg_match_all('!\d+!', $string, $matches);
        
        return ! empty($matches[0]) ? implode($separator, $matches[0]) : NULL;
    }

    /**
     * Check if string is of minimum length.
     *
     * @param string $string            
     * @param int $length            
     *
     * @return bool
     */
    public function HasLengthMin($string, $length = 0)
    {
        
        /**
         * Set function name.
         * Check if mbsting extension is available.
         *
         * @var string
         */
        $f = (function_exists('mb_internal_encoding')) ? 'mb_strlen' : 'strlen';
        
        /**
         * Check data length.
         * Return result.
         */
        return ($f($string) < $length) ? FALSE : TRUE;
    }

    /**
     * Check if string is of maximum length.
     *
     * @param string $string            
     * @param int $length            
     *
     * @return bool
     */
    public function HasLengthMax($string, $length = 0)
    {
        
        /**
         * Set function name.
         * Check if mbsting extension is available.
         *
         * @var string
         */
        $f = (function_exists('mb_internal_encoding')) ? 'mb_strlen' : 'strlen';
        
        /**
         * Check data length.
         * Return result.
         */
        return ($f($string) > $length) ? FALSE : TRUE;
    }

    /**
     * Check if string has the minimum word length.
     *
     * @param string $string            
     * @param number $length            
     * @return boolean
     */
    public function HasWordsMin($string, $length = 0)
    {
        $word_count = $this->GetWordCount($string);
        
        return $length > $word_count ? FALSE : TRUE;
    }

    /**
     * Check if string has the maximum word length.
     *
     * @param string $string            
     * @param number $length            
     * @return boolean
     */
    public function HasWordsMax($string, $length = 0)
    {
        $word_count = $this->GetWordCount($string);
        
        return $word_count > $length ? FALSE : TRUE;
    }

    /**
     * Check if two strings are equal.
     *
     * @param stirng $string            
     * @param string $match            
     *
     * @return bool
     */
    public function Equals($string, $match)
    {
        
        /**
         * Check if data is the same as the match.
         * Return result.
         */
        return ($string !== $match) ? FALSE : TRUE;
    }

    /**
     * Check is string contains both letters and numbers.
     *
     * @param string $string            
     *
     * @return bool
     */
    public function IsAlphanumeric($string)
    {
        
        /**
         * Set condition.
         */
        $match = "/^.*(?=.*\d)(?=.*[A-z]).*$/";
        
        /**
         * Check if data is alphanumeric.
         * Return result.
         */
        return (preg_match($match, $string)) ? TRUE : FALSE;
    }

    /**
     * Check if a string is numeric.
     *
     * @param string $string            
     *
     * @return boolean
     */
    public function IsNumeric($string)
    {
        return is_numeric($string);
    }

    /**
     * Check if string contains an integer.
     *
     * @param string $string            
     *
     * @return boolean
     */
    public function IsInteger($string)
    {
        return (ctype_digit($string) || is_int($string));
    }

    /**
     * Check if string is a valid email address.
     *
     * @param string $string            
     *
     * @return bool
     */
    public function IsValidEmail($string)
    {
        return (filter_var($string, FILTER_VALIDATE_EMAIL)) ? TRUE : FALSE;
    }

    /**
     * Check if string matches captcha.
     *
     * @param string $string            
     *
     * @return bool
     */
    public function MatchesCaptcha($string)
    {
        
        /**
         *
         * @var string
         */
        $captcha = Core::Captcha()->GetToken();
        
        /**
         * Check if data is the same as the match.
         * Return result.
         */
        return ($string != $captcha) ? FALSE : TRUE;
    }

    /**
     * Get an array with all words in a string.
     * Unlike the native function this also cosiders numbers as words
     * and works with UTF strings
     *
     * @param string $string            
     *
     * @return array
     */
    public function GetWords($string)
    {
        preg_match_all("/\S+/", $string, $matches);
        
        return ! empty($matches[0]) ? $matches[0] : array();
    }

    /**
     * Get number of words in a string.
     * Unlike the native function this also cosiders numbers as words
     * and works with UTF strings
     *
     * @param string $string            
     * @return number
     */
    public function GetWordCount($string)
    {
        $words = $this->GetWords($string);
        
        return count($words);
    }

    /**
     * Encode URL string.
     *
     * @param string $string            
     *
     * @return string
     */
    public function EncodeUrlString($string)
    {
        return strtr(base64_encode($string), '+/=', '-_.');
    }

    /**
     * Decode URL string.
     *
     * @param string $string            
     *
     * @return string
     */
    public function DecodeUrlString($string)
    {
        
        /**
         *
         * @var string
         */
        $url_string = base64_decode(strtr($string, '-_.', '+/='));
        
        return $url_string;
    
    /**
     * DISABLED, NO WORK Return decoded URL string only if valid.
     * return preg_match("/^[a-zA-Z0-9\/\-\_.]+$/", $url_string) ? $url_string : '/';
     */
    }

    /**
     * Return a short string based on the full string.
     * It can work with character limit, word limit, or both.
     * Eg. get max 80 characters: Truncate($string, 80)
     * Eg. get max 10 words: Truncate($string, NULL, 10)
     * Eg. get max 80 characters but no more than 10 words: Truncate($string, 80, 10)
     *
     * @param string $string
     *            initial string
     * @param int $chars
     *            character limit
     * @param int $words
     *            word limit
     * @param string $add
     *            what to add after the string
     * @return string result
     */
    public function Truncate($string, $chars = 0, $words = 0, $add = ' ...')
    {
        
        /**
         * Initialize string.
         */
        $data = $string;
        
        /**
         * Check string length
         */
        if (strlen($string) <= $chars) {
            /**
             * Return unmodified string.
             */
            return $data;
        }
        /**
         * Check for character limit.
         */
        if (! empty($chars)) {
            /**
             * Set raw string.
             * Chop it off at chars limit.
             */
            $raw = substr($string, 0, $chars);
            /**
             * Set new string.
             */
            $data = substr($raw, 0, strrpos($raw, ' '));
        }
        
        /**
         * Check if we have words limit.
         */
        if (! empty($words)) {
            
            $raw = str_word_count($data, 2, ',.!');
            /**
             * Set new string.
             */
            $data = implode(' ', array_slice($raw, 0, $words));
        }
        
        /**
         * Return result.
         */
        return $data . $add;
    }

    /**
     * Add html links to a string
     *
     * @param string $string            
     * @return string
     */
    public function Linkify($string, $extra = NULL)
    {
        $template = ! empty($extra) ? "<a href=\"\\0\" {$extra}>\\0</a>" : "<a href=\"\\0\">\\0</a>";
        
        return preg_replace("~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~", $template, $string);
    }
}
?>