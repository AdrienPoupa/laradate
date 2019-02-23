<?php
namespace App;

class Utils {

    /**
     * Function allowing to generate poll's url
     * @param   string $id The poll's id
     * @param   bool $admin True to generate an admin URL, false for a public one
     * @param   string $voteId (optional) The vote's unique id
     * @param null $action
     * @param null $actionValue
     * @return string The poll's URL.
     */
    public static function getPollUrl($id, $admin = false, $voteId = '', $action = null, $actionValue = null) {
        if ($admin === true) {
            $url = url('poll/' . $id . '/admin');
        } else {
            $url = url('poll/' . $id);
        }
        if ($voteId != '') {
            $url .= '/vote/' . $voteId . "#edit";
        } elseif ($action != null) {
            if ($actionValue != null) {
                $url .= '/' . $action . '/' . $actionValue;
            } else {
                $url .= '/' . $action;
            }
        }

        return $url;
    }

    public static function table($tableName) {
        return $tableName;
    }

    public static function markdown($md, $clear) {
        preg_match_all('/\[!\[(.*?)\]\((.*?)\)\]\((.*?)\)/', $md, $md_a_img); // Markdown [![alt](src)](href)
        preg_match_all('/!\[(.*?)\]\((.*?)\)/', $md, $md_img); // Markdown ![alt](src)
        preg_match_all('/\[(.*?)\]\((.*?)\)/', $md, $md_a); // Markdown [text](href)
        if (isset($md_a_img[2][0]) && $md_a_img[2][0] != '' && isset($md_a_img[3][0]) && $md_a_img[3][0] != '') { // [![alt](src)](href)

            $text = self::htmlEscape($md_a_img[1][0]);
            $html = '<a href="' . self::htmlEscape($md_a_img[3][0]) . '"><img src="' . self::htmlEscape($md_a_img[2][0]) . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" /></a>';

        } elseif (isset($md_img[2][0]) && $md_img[2][0] != '') { // ![alt](src)

            $text = self::htmlEscape($md_img[1][0]);
            $html = '<img src="' . self::htmlEscape($md_img[2][0]) . '" class="img-responsive" alt="' . $text . '" title="' . $text . '" />';

        } elseif (isset($md_a[2][0]) && $md_a[2][0] != '') { // [text](href)

            $text = self::htmlEscape($md_a[1][0]);
            $html = '<a href="' . $md_a[2][0] . '">' . $text . '</a>';

        } else { // text only

            $text = self::htmlEscape($md);
            $html = $text;

        }

        return $clear ? $text : $html;
    }

    public static function htmlEscape($html) {
        return htmlentities($html, ENT_HTML5 | ENT_QUOTES);
    }

    public static function csvEscape($text) {
        $escaped = str_replace('"', '""', $text);
        $escaped = str_replace("\r\n", '', $escaped);
        $escaped = str_replace("\n", '', $escaped);
        $escaped = preg_replace("/^(=|\+|\-|\@)/", "'$1", $escaped);

        return '"' . $escaped . '"';
    }

    public static function cleanFilename($title) {
        $cleaned = preg_replace('[^a-zA-Z0-9._-]', '_', $title);
        $cleaned = preg_replace(' {2,}', ' ', $cleaned);

        return $cleaned;
    }

    public static function base64url_encode($input) {
        return rtrim(strtr(base64_encode($input), '+/', '-_'), '=');
    }

    public static function base64url_decode($input) {
        return base64_decode(str_pad(strtr($input, '-_', '+/'), strlen($input) % 4, '=', STR_PAD_RIGHT));
    }

    /**
     * This method filter an array calling "filter_var" on each items.
     * Only items validated are added at their own indexes, the others are not returned.
     * @param array $arr The array to filter
     * @param int $type The type of filter to apply
     * @param array|null $options The associative array of options
     * @return array The filtered array
     */
    public static function filterArray(array $arr, $type, $options = null) {
        $newArr = [];

        foreach($arr as $id=>$item) {
            $item = filter_var($item, $type, $options);
            if ($item !== false) {
                $newArr[$id] = $item;
            }
        }

        return $newArr;
    }

    /**
     * Check if a MD5 hash is valid
     * @param string $md5 the hash to check
     * @return int|string hash if valid, 0 otherwise
     */
    public static function isValidMd5($md5 ='') {
        if (preg_match('/^[a-f0-9]{32}$/', $md5)) {
            return $md5;
        }
        return 0;
    }

    /**
     * @param $value
     * @param array $allowedValues
     * @return null
     */
    public static function filterAllowedValues($value, array $allowedValues) {
        return in_array($value, $allowedValues, true) ? $value : null;
    }
}
