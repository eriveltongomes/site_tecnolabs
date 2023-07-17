<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 400 Bad Request responses
 *
 * @package Requests
 */

/**
 * Exception for 400 Bad Request responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_400 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 400;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Bad Request';
}
