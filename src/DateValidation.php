<?php

namespace ChristianBerkman\DateValidation;

use DateTime;
use DateTimeZone;
use InvalidArgumentException;

class DateValidation
{
    /**
     * ----------------------------------------------------
     * Internal methods and properties
     * ----------------------------------------------------
     */
    /**
     * Public static strings
     */
    public static string $invalidDate = 'Invalid date.';

    public static string $compareInvalid = 'Comparing to invalid date in field: ';

    /**
     * Checks for a valid date using a given format and returns a DateTime object
     * Time information is discarded and set to midnight
     * Based on CodeIgniter4/framework/sytem/Validation/FormatRules::valid_date
     *
     * @param string|null           $str
     * @param non-empty-string|null $format
     */
    public static function createDate(string $value, ?string $format = null): bool|DateTime
    {
        if ($value === '') {
            return false;
        }

        if ($format === null || $format === '') {
            if (! strtotime($value)) {
                return false;
            }
            $date = new DateTime($value);
        } else {
            $date = DateTime::createFromFormat($format, $value);
        }

        if ($date === false) {
            return false;
        }

        $errors = DateTime::getLastErrors();
        if ($errors['warning_count'] !== 0 || $errors['error_count'] !== 0) {
            return false;
        }

        $date->setTimeZone(new DateTimeZone('UTC'))->setTime(0, 0);

        return $date;
    }

    /**
     * Return a DateTime object of today's date at midnight
     */
    public static function today(): DateTime
    {
        return (new DateTime('today'))->setTimeZone(new DateTimeZone('UTC'))->setTime(0, 0);
    }

    public static function splitParams(string $params, array $data = [], ?string &$field = null, ?string &$fieldValue = null, &$format = null): void
    {
        // Split params
        $params = explode(',', $params);
        $field  = $params[0] ?? '';
        if ($field === '') {
            throw new InvalidArgumentException('You must supply the parameters: field.');
        }
        $format = $params[1] ?? '';

        $fieldValue = dot_array_search($field, $data);
    }

    /**
     * ----------------------------------------------------
     * Comparing value date to today
     * ----------------------------------------------------
     */

    /**
     * Validates if the value date is before today
     */
    public function date_before_today(string $value, string $format, array $data, ?string &$error): bool
    {
        // Check value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        if (! ($valueDate->getTimestamp() < static::today()->getTimestamp())) {
            $error = 'Date must be before today.';

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is on or before today
     */
    public function date_ending_today(string $value, string $format, array $data, ?string &$error): bool
    {
        // Check value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        if (! ($valueDate->getTimestamp() <= static::today()->getTimestamp())) {
            $error = 'Date must be on or before today.';

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is on or after today
     */
    public function date_starting_today(string $value, string $format, array $data, ?string &$error): bool
    {
        // Check value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Compare value date to today
        if ($valueDate->getTimestamp() < static::today()->getTimestamp()) {
            $error = 'Date must be on or after today.';

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is after today
     */
    public function date_after_today(string $value, string $format, array $data, ?string &$error): bool
    {
        // Check value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        if ($valueDate->getTimestamp() <= static::today()->getTimestamp()) {
            $error = 'Date must be after today.';

            return false;
        }

        return true;
    }

    /**
     * ----------------------------------------------------
     * Comparing value date to a field date
     * ----------------------------------------------------
     */

    /**
     * Validates if the value date is before a field date
     *
     * @param array $data field,format
     */
    public function date_before(string $value, string $params, array $data, ?string &$error): bool
    {
        static::splitParams($params, $data, $field, $fieldValue, $format);

        // Value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Field date
        if (! $fieldDate = static::createDate($fieldValue, $format)) {
            $error = static::$compareInvalid . $field;

            return false;
        }

        // Compare
        if (! ($valueDate->getTimestamp() < $fieldDate->getTimestamp())) {
            $error = "Date must be before field: {$field}.";

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is on or before a field date
     *
     * @param array $data field,format
     */
    public function date_ending(string $value, string $params, array $data, ?string &$error): bool
    {
        static::splitParams($params, $data, $field, $fieldValue, $format);

        // Value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Field date
        if (! $fieldDate = static::createDate($fieldValue, $format)) {
            $error = static::$compareInvalid . $field;

            return false;
        }

        // Compare
        if (! ($valueDate->getTimestamp() <= $fieldDate->getTimestamp())) {
            $error = "Date must be on or before field: {$field}.";

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is on or after a field date
     *
     * @param array $data field,format
     */
    public function date_starting(string $value, string $params, array $data, ?string &$error): bool
    {
        static::splitParams($params, $data, $field, $fieldValue, $format);

        // Value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Field date
        if (! $fieldDate = static::createDate($fieldValue, $format)) {
            $error = static::$compareInvalid . $field;

            return false;
        }

        // Compare
        if (! ($valueDate->getTimestamp() >= $fieldDate->getTimestamp())) {
            $error = "Date must be on or  after field: {$field}.";

            return false;
        }

        return true;
    }

    /**
     * Validates if the value date is after a field date
     *
     * @param array $data field,format
     */
    public function date_after(string $value, string $params, array $data, ?string &$error): bool
    {
        static::splitParams($params, $data, $field, $fieldValue, $format);

        // Value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Field date
        if (! $fieldDate = static::createDate($fieldValue, $format)) {
            $error = static::$compareInvalid . $field;

            return false;
        }

        // Compare
        if (! ($valueDate->getTimestamp() > $fieldDate->getTimestamp())) {
            $error = "Date must be on or  after field: {$field}.";

            return false;
        }

        return true;
    }

    /**
     * ----------------------------------------------------
     * Other
     * ----------------------------------------------------
     */

    /**
     * Validate if the value date is one of the listed weekdays
     *
     * @param string $params format,dow1,dow2,...
     */
    public function date_on_dow(string $value, string $params, array $data, ?string &$error): bool
    {
        // Split params
        $params = explode(',', $params);
        $format = $params[0];
        if ($format === 'none') {
            $format = '';
        }
        array_shift($params);
        $weekdays = array_map('intval', $params);

        if (count($weekdays) === 0) {
            return false;
        }

        // Check value date
        if (! $valueDate = static::createDate($value, $format)) {
            $error = static::$invalidDate;

            return false;
        }

        // Return true if value date is on a week day in $weekdays
        $weekday = (int) $valueDate->format('N');
        if (! in_array($weekday, $weekdays, true)) {
            $error = 'Day of the week is not allowed';

            return false;
        }

        return true;
    }
}
