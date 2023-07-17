<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Http;

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 417 Expectation Failed responses
 *
 * @package Requests\Exceptions
 */
final class Status417 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 417;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Expectation Failed';
}
