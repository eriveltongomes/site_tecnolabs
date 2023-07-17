<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Requests
 */

/**
 * Exception for 511 Network Authentication Required responses
 *
 * @see https://tools.ietf.org/html/rfc6585
 * @package Requests
 */
class Requests_Exception_HTTP_511 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 511;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Network Authentication Required';
}
