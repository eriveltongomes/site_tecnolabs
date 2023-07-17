<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Requests
 */

/**
 * Exception for 502 Bad Gateway responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_502 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 502;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Gateway';
}
