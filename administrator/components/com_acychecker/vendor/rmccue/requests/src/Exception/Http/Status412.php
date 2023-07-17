<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php
/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests\Exceptions
 */

namespace WpOrg\Requests\Exception\Http;

use WpOrg\Requests\Exception\Http;

/**
 * Exception for 412 Precondition Failed responses
 *
 * @package Requests\Exceptions
 */
final class Status412 extends Http {
	/**
	 * HTTP status code
	 *
	 * @var integer
	 */
	protected $code = 412;

	/**
	 * Reason phrase
	 *
	 * @var string
	 */
	protected $reason = 'Precondition Failed';
}
