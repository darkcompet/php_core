<?php

namespace App\Tool\Compet\Core;

use DateTime;

class DkDateTimes {
    const FORMAT = 'Y-m-d H:i:s';
    const FORMAT_YMD = 'Y-m-d';
    const FORMAT_HIS = 'H:i:s';

    /**
     * @return int Time in seconds.
     */
    public static function now() {
        return time();
    }

    /**
     * Compare diff-value between 2 datetimes.
     * Caller can make params under `string` or `datetime` type,
     * for eg,. date('Y-m-d H:i:s') or '2020-10-09 13:35:59'.
     * 
     * @param string|DateTime $a_datetime Datetime in format `Y-m-d H:i:s`, can be VALID string or VALID datetime (日付型が正しいことが前提).
     * @param string|DateTime $b_datetime Datetime in format `Y-m-d H:i:s`, can be VALID string or VALID datetime (日付型が正しいことが前提).
     * @param string|array $Ymd_noise_delimiters Target separator should be converted to `-` in Ymd format
     * @param string|array $His_noise_delimiters Target separator should be converted to `:` in His format
     * @return int Diff value between given `a_datetime` and `b_datetime`. That is, if result is greater than 0,
     * then `a_datetime` > `b_datetime`, if result is 0 then they are equal, otherwise `b_datetime` > `a_datetime`.
     */
    public static function compare($a_datetime, $b_datetime, $Ymd_noise_delimiters = ['/'], $His_noise_delimiters = ['-']) {
        return self::calcUnixTimestamp($a_datetime, $Ymd_noise_delimiters, $His_noise_delimiters)
            - self::calcUnixTimestamp($b_datetime, $Ymd_noise_delimiters, $His_noise_delimiters);
    }

    /**
     * Compare diff-value between 2 part-datetimes Ymd and His.
     * Caller can make params under `string` or `datetime` type,
     * for eg,. date('Y-m-d H:i:s') or '2020-10-09 13:35:59'.
     * 
     * @param string|DateTime $a_datetime Datetime in format `Y-m-d H:i:s`, can be VALID string or VALID datetime (日付型が正しいことが前提).
     * @param string|DateTime $b_datetime Datetime in format `Y-m-d H:i:s`, can be VALID string or VALID datetime (日付型が正しいことが前提).
     * @param string|array $Ymd_noise_delimiters Target separator should be converted to `-` in Ymd format
     * @param string|array $His_noise_delimiters Target separator should be converted to `:` in His format
     * @return int Diff value between given `a_datetime` and `b_datetime`. That is, if result is greater than 0,
     * then `a_datetime` > `b_datetime`, if result is 0 then they are equal, otherwise `b_datetime` > `a_datetime`.
     */
    public static function compareParts($a_datetime_Ymd, $a_datetime_His, $b_datetime_Ymd, $b_datetime_His, $Ymd_noise_delimiters = ['/'], $His_noise_delimiters = ['-']) {
        return self::calcUnixTimestampFromParts($a_datetime_Ymd, $a_datetime_His, $Ymd_noise_delimiters, $His_noise_delimiters)
            - self::calcUnixTimestampFromParts($b_datetime_Ymd, $b_datetime_His, $Ymd_noise_delimiters, $His_noise_delimiters);
    }

    /**
     * @param string|datetime $YmdHis_datetime Format should be `Y-m-d H:i:s`, characters after `s` will be ignored.
     * @param string|array $Ymd_noise_delimiters Target separator should be converted to `-` in Ymd format
     * @param string|array $His_noise_delimiters Target separator should be converted to `:` in His format
     */
    public static function calcUnixTimestamp($YmdHis_datetime, $Ymd_noise_delimiters = ['/'], $His_noise_delimiters = ['-']) {
        $YmdHis = self::extractDateTime($YmdHis_datetime, $Ymd_noise_delimiters, $His_noise_delimiters);
        $Ymd = $YmdHis[0];
        $His = $YmdHis[1];
        return self::makeUnixTime($Ymd[0], $Ymd[1], $Ymd[2], $His[0], $His[1], $His[2]);
    }

    /**
     * @param string|datetime $Ymd_datetime Format should be `Y-m-d` or `Y/m/d`, characters after `d` will be ignored.
     * @param string|datetime $His_datetime Format should be `H:i:s` or `H-i-s`, characters after `s` will be ignored.
     * @param string|array $Ymd_noise_delimiters Target separator should be converted to `-` in Ymd format
     * @param string|array $His_noise_delimiters Target separator should be converted to `:` in His format
     */
    public static function calcUnixTimestampFromParts($Ymd_datetime, $His_datetime, $Ymd_noise_delimiters = ['/'], $His_noise_delimiters = ['-']) {
        $Ymd = self::extractDate($Ymd_datetime, $Ymd_noise_delimiters);
        $His = self::extractTime($His_datetime, $His_noise_delimiters);
        return self::makeUnixTime($Ymd[0], $Ymd[1], $Ymd[2], $His[0], $His[1], $His[2]);
    }

    private static function makeUnixTime($year, $month, $day, $hour, $minute, $second) {
        return mktime((int) $hour, (int) $minute, (int) $second, (int) $month, (int) $day, (int) $year);
    }

    /**
     * This tries to extract given `datetime` to array [Year, month, day, Hour, minute, second]
     * @param string|datetime $datetime Format should be `Y-m-d H:i:s`, should contain a `space` between 2 parts.
     * @return array [[Y, m, d], [H, i, s]]
     */
    public static function extractDateTime($datetime, $Ymd_noise_delimiters = ['/'], $His_noise_delimiters = ['-']) {
        $Ymd_and_His = explode(' ', $datetime);
        return [
            self::extractDate($Ymd_and_His[0] ?? '0-0-0', $Ymd_noise_delimiters),
            self::extractTime($Ymd_and_His[1] ?? '0:0:0', $His_noise_delimiters)
        ];
    }

    /**
     * This tries to extract given `date` to array [Year, month, day]
     * @param string|datetime $Ymd_date Format should be `Y-m-d`.
     * @return array [Y, m, d]
     */
    public static function extractDate($Ymd_date, $Ymd_noise_delimiters = ['/']) {
        $Ymd = explode('-', str_replace($Ymd_noise_delimiters, '-', $Ymd_date));
        return [$Ymd[0] ?? 0, $Ymd[1] ?? 0, $Ymd[2] ?? 0];
    }

    /**
     * This tries to extract given `time` to array [Hour, minute, second]
     * @param string|datetime $His_time Format should be `H:i:s`.
     * @return array [H, i, s]
     */
    public static function extractTime($His_time, $His_noise_delimiters = ['-']) {
        $His = explode(':', str_replace($His_noise_delimiters, ':', $His_time));
        return [$His[0] ?? 0, $His[1] ?? 0, $His[2] ?? 0];
    }

    /**
     * Convert date-in-text to `date` object.
     * For eg,. `text2date('2020-12-30')` will return `date` object of `2020-12-30 00:00:00`.
     * @param string $dateText date in text format.
     * @param string $result_format date format will be applied to result.
     * @return date object
     */
    public static function text2date($dateText, $result_format = self::FORMAT) {
        return date($result_format, strtotime($dateText));
    }

    /**
     * Add more days to given date in text format.
     * For eg,. `addDays('2020-12-20', 4)` will return `date` object of `2020-12-24 00:00:00`.
     * @param string $dateText date in text format.
     * @param int extra days, can be positive, negative or 0.
     * @param string $result_format date format will be applied to result.
     * @return date object
     */
    public static function addDays($dateText, $extraDays, $result_format = self::FORMAT) {
        return date($result_format, strtotime("$dateText + $extraDays days"));
    }

    public static function subDays($dateText, $extraDays, $result_format = self::FORMAT) {
        return date($result_format, strtotime("$dateText - $extraDays days"));
    }

    /**
     * Get Unix timestamp in nano seconds from given text in given format.
     * For eg,. `text2time('2020-12-29 11:58:59', 'Y-m-d H:i:s')` will return Unix timestamp `1609271939`.
     *
     * @param string $time_in_text
     * @param string $format
     * @param DateTimeZone $timezone
     * @return int
     */
    public static function text2time($time_in_text, $format, $timezone = null) {
        return DateTime::createFromFormat($format, $time_in_text, $timezone)->getTimestamp();
    }
    
    /**
     * Split a duration to dates.
     * 
     * @param string $start_date
     * @param string $end_date
     * 
     * @return array Array of date
     */
    public static function splitByDay($start_date, $end_date) {
        $dates = [];
        $current_date = $start_date;
        while ($current_date <= $end_date) {
            $dates[] = $current_date;
            $current_date = date("Y-m-d", strtotime("$current_date + 1 days"));
        }
        return $dates;
    }
}
