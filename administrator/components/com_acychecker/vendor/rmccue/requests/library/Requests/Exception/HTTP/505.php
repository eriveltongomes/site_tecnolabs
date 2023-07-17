<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Requests
 */

/**
 * Exception for 505 HTTP Version Not Supported responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_505 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 505;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'HTTP Version Not Supported';
}
