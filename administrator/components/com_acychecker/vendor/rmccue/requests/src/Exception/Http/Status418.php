<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 418 I'm A Teapot responses
 *
 * @link https://tools.ietf.org/html/rfc2324
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Http;

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 418 I'm A Teapot responses
 *
 * @link https://tools.ietf.org/html/rfc2324
 *
 * @package Requests\Exceptions
 */
final class Status418 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 418;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = "I'm A Teapot";
}
