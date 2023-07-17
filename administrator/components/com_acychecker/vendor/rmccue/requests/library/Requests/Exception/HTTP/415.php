<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */

/**
 * Exception for 415 Unsupported Media Type responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_415 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 415;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Unsupported Media Type';
}
