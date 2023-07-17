<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Requests
 */

/**
 * Exception for 413 Request Entity Too Large responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_413 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 413;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Request Entity Too Large';
}
