<?php
namespace Util\Email;

class Decode {
    /**
     * If you change one of these, please check the other for fixes as well
     *
     * @const Pattern to match RFC 2047 charset encodings in mail headers
     */
    const rfc2047header = '/=\?([^ ?]+)\?([BQbq])\?([^ ?]+)\?=/';

    const rfc2047header_spaces = '/(=\?[^ ?]+\?[BQbq]\?[^ ?]+\?=)\s+(=\?[^ ?]+\?[BQbq]\?[^ ?]+\?=)/';

    /**
     * http://www.rfc-archive.org/getrfc.php?rfc=2047
     *
     * =?<charset>?<encoding>?<data>?=
     *
     * @param string $header
     */
    public static function is_encoded_header($header) {
        // e.g. =?utf-8?q?Re=3a=20Support=3a=204D09EE9A=20=2d=20Re=3a=20Support=3a=204D078032=20=2d=20Wordpress=20Plugin?=
        // e.g. =?utf-8?q?Wordpress=20Plugin?=
        return preg_match(self::rfc2047header, $header) !== 0;
    }

    public static function header_charsets($header) {
        $matches = null;
        if (!preg_match_all(self::rfc2047header, $header, $matches, PREG_PATTERN_ORDER)) {
            return array();
        }
        return array_map('strtoupper', $matches[1]);
    }

    public static function decode_header($header) {
        $matches = null;

        /* Repair instances where two encodings are together and separated by a space (strip the spaces) */
        $header = preg_replace(self::rfc2047header_spaces, "$1$2", $header);

        /* Now see if any encodings exist and match them */
        if (!preg_match_all(self::rfc2047header, $header, $matches, PREG_SET_ORDER)) {
            return $header;
        }
        foreach ($matches as $header_match) {
            list($match, $charset, $encoding, $data) = $header_match;
            $encoding = strtoupper($encoding);
            switch ($encoding) {
                case 'B':
                    $data = base64_decode($data);
                    break;
                case 'Q':
                    $data = quoted_printable_decode(str_replace("_", " ", $data));
                    break;
                default:
                    throw new Exception("preg_match_all is busted: didn't find B or Q in encoding $header");
            }
            // This part needs to handle every charset
            switch (strtoupper($charset)) {
                case "UTF-8":
                    break;
                default:
                    /* Here's where you should handle other character sets! */
                    throw new Exception("Unknown charset in header - time to write some code.");
            }
            $header = str_replace($match, $data, $header);
        }
        return $header;
    }
}