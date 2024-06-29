<?php

namespace ChristianBerkman\DateValidation;

use \DateTime;
use \DateTimeZone;

class DateValidation
{
	/**
	 * Public static strings
	 */
	public static string $invalidDate = 'Invalid date.';

	/**
	 * Checks for a valid date using a given format and returns a DateTime object
	 * Time information is discarded and set to midnight
	 * Based on CodeIgniter4/framework/sytem/Validation/FormatRules::valid_date
	 *
	 * @param string|null           $str
	 * @param non-empty-string|null $format
	 * @return DateTime|bool
	 */
	public static function createDate(string $value, ?string $format = null): DateTime|bool
	{
		if ($value === '') {
			return false;
		}

		if ($format === null || $format === '') {
			if (!strtotime($value)) return false;
			$date = new DateTime($value);
		} else {
			$date = DateTime::createFromFormat($format, $value);
		}

		if ($date === false) {
			return false;
		}

		$errors = DateTime::getLastErrors();
		if ($errors['warning_count'] !== 0 || $errors['error_count'] !== 0) return false;

		return $date->setTimeZone(new DateTimezone('UTC'))->setTime(0, 0);
	}

	/**
	 * Return a DateTime object of today's date at midnight
	 *
	 * @return DateTime
	 */
	public static function today(): DateTime
	{
		return (new DateTime('today'))->setTimeZone(new DateTimezone('UTC'))->setTime(0, 0);
	}

/**
 * ----------------------------------------------------
 * Comparing value date to today
 * ----------------------------------------------------
 */

	/**
	 * Validates if the value date is on or after today
	 *
	 * @param string $value
	 * @param string $format
	 * @param array $data
	 * @param string|null $error
	 * @return bool
	 */
	public function date_starting_today(string $value, string $format, array $data, ?string &$error): bool
	{
		// Check value date
		if (!$valueDate = static::createDate($value, $format)) {
			$error = static::$invalidDate;
			return false;
		}

		// Compare value date to today
		if ($valueDate->getTimestamp() < static::today()->getTimestamp()) {
			$error = "Date must be on or after today.";
			return false;
		}

		return true;
	}

	/**
	 * Validates if the value date is after today
	 *
	 * @param string $value
	 * @param string $format
	 * @param array $data
	 * @param string|null $error
	 * @return bool
	 */
	public function date_after_today(string $value, string $format, array $data, ?string &$error): bool
	{
		// Check value date
		if (!$valueDate = static::createDate($value, $format)) {
			$error = static::$invalidDate;
			return false;
		}

		if ($valueDate->getTimestamp() <= static::today()->getTimestamp()) {
			$error = "Date must be after today.";
			return false;
		}

		return true;
	}
/**
 * ----------------------------------------------------
 * Comparing value date to a field date
 * ----------------------------------------------------
 */

}
