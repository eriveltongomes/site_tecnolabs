<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Requests
 */

/**
 * Exception for 407 Proxy Authentication Required responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_407 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 407;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Proxy Authentication Required';
}
