<?php

namespace App\Tool\Compet\Core;

/**
 * Helper for string.
 *
 * @author darkcompet
 */
class DkStrings {
	/**
	 * @return int Length of string (char count).
	 */
	public static function length($text) {
		return mb_strlen($text);
	}
	
    /**
     * @param string $text
     * @param string $targetChar Delimiter char.
     * @return string|false
     */
    public static function trim($text, $targetChar) {
        if ($text == null) {
            return null;
        }

        $N = mb_strlen($text);
        $start = 0;
        $end = $N - 1;

        while ($start <= $end && $text[$start] == $targetChar) {
            ++$start;
        }
        while ($end >= $start && $text[$end] == $targetChar) {
            --$end;
        }

        return ($start > 0 || $end < $N - 1) ? self::substring($text, $start, $end - $start + 1) : $text;
    }

    /**
     * @param string $text
     * @param string|null $delimiter
     * @return array|false False if `delimiter` is null or empty. Otherwise return array of string.
     */
    public static function split($text, $delimiter) {
        if (! $text || ! $delimiter) {
            return $text;
        }
        return explode($delimiter, $text);
    }

    /**
     * @param array $items an array of string element
     * @param string $separator like ":", "div"...
     * @return string
     */
    public static function join($items, $separator) {
        return implode($separator, $items);
    }

    /**
	 * Get substring from an index to a position.
	 * 
     * @param string $text Target string
     * @param int $startIndex Inclusive index, should <= `endIndex` and should NOT be negative.
     * @param int $endIndex Exclusive index, should >= `startIndex`.
     * @return string
     */
    public static function substring($text, $startIndex, $endIndex) {
        return mb_substr($text, $startIndex, $endIndex - $startIndex);
    }

    /**
     * @param string $text
     * @param string $target
     * @param string $replacement
     * @return string|string[] Result string, or array for multiple replacement.
     */
    public static function replace($text, $target, $replacement) {
        return str_replace($target, $replacement, $text);
    }
	
	/**
	 * @return boolean True iff the `text` is started with given `target`.
	 */
	public static function startsWith($text, $sample) {
		$sampleCount = mb_strlen($sample);
		$totalCount = mb_strlen($text);
		if ($sampleCount > $totalCount) {
			return false;
		}
		return $sample === self::substring($text, 0, $sampleCount);
	}
	
	/**
	 * @return boolean True iff the `text` is started with given `sample`.
	 */
	public static function endsWith($text, $sample) {
		$totalCount = mb_strlen($text);
		$fromIndex = $totalCount - mb_strlen($sample);
		if ($fromIndex < 0) {
			return false;
		}
		return $sample === self::substring($text, $fromIndex, $totalCount);
	}

    /**
     * Replace a substring which be expressed in regular expression with a replacement in given text.
     *
     * @param string $text
     * @param string $pattern like "/:/"
     * @param string $replacement like "new"
     * @return string|string[]|null
     */
    public static function replaceRegex($text, $pattern, $replacement) {
        return preg_replace($pattern, $replacement, $text);
    }

    /**
     * @param string $format For eg,. "There are %d fields in class %s"
     * @param array $args List of argument
     * @return string
     */
    public static function format($format, ...$args) {
        return sprintf($format, $args);
    }

    /**
     * Convert html-special chars: & (アンパサンド)、< (小なり)、> (大なり)、' (シングルクォート)、" (ダブルクォート)
     * to html symbol which can be displayed normal in webpage.
     * In other words, this makes given `text` can be displayed well in html page.
     *
     * Refer: https://www.php.net/manual/ja/function.htmlspecialchars.php
     */
    public static function convertHtmlSpecialChars($text, $tag = ENT_QUOTES) {
        return htmlspecialchars($text, $tag);
    }

    public static function convertAllHtmlSpecialChars($text) {
        return htmlentities($text);
    }
}
