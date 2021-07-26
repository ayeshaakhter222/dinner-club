<?php

/**
 * Raw Signature Handler
 *
 * PHP version 5
 *
 * Handles signatures as arrays
 *
 * @category  Crypt
 * @package   Common
 * @author    Jim Wigginton <terrafrost@php.net>
 * @copyright 2016 Jim Wigginton
 * @license   http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link      http://phpseclib.sourceforge.net
 */
namespace WPMailSMTP\Vendor\phpseclib3\Crypt\Common\Formats\Signature;

use WPMailSMTP\Vendor\phpseclib3\Math\BigInteger;
/**
 * Raw Signature Handler
 *
 * @package Common
 * @author  Jim Wigginton <terrafrost@php.net>
 * @access  public
 */
abstract class Raw
{
    /**
     * Loads a signature
     *
     * @access public
     * @param array $sig
     * @return array|bool
     */
    public static function load($sig)
    {
        switch (\true) {
            case !\is_array($sig):
            case !isset($sig['r']) || !isset($sig['s']):
            case !$sig['r'] instanceof \WPMailSMTP\Vendor\phpseclib3\Math\BigInteger:
            case !$sig['s'] instanceof \WPMailSMTP\Vendor\phpseclib3\Math\BigInteger:
                return \false;
        }
        return ['r' => $sig['r'], 's' => $sig['s']];
    }
    /**
     * Returns a signature in the appropriate format
     *
     * @access public
     * @param \phpseclib3\Math\BigInteger $r
     * @param \phpseclib3\Math\BigInteger $s
     * @return string
     */
    public static function save(\WPMailSMTP\Vendor\phpseclib3\Math\BigInteger $r, \WPMailSMTP\Vendor\phpseclib3\Math\BigInteger $s)
    {
        return \compact('r', 's');
    }
}
