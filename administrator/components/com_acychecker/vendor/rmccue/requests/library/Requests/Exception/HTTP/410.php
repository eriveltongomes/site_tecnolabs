<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 410 Gone responses
 *
 * @package Requests
 */

/**
 * Exception for 410 Gone responses
 *
 * @package Requests
 */
class Requests_Exception_HTTP_410 extends Requests_Exception_HTTP {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 410;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Gone';
}
