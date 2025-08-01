<?php
/**
 * @license BSD-3-Clause
 *
 * Modified using {@see https://github.com/BrianHenryIE/strauss}.
 */

declare(strict_types=1);

namespace KadenceWP\KadenceStarterTemplates\Dotenv\Util;

use KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Error;
use KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Success;

/**
 * @internal
 */
final class Regex
{
    /**
     * This class is a singleton.
     *
     * @codeCoverageIgnore
     *
     * @return void
     */
    private function __construct()
    {
        //
    }

    /**
     * Perform a preg match, wrapping up the result.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<bool, string>
     */
    public static function matches(string $pattern, string $subject)
    {
        return self::pregAndWrap(static function (string $subject) use ($pattern) {
            return @\preg_match($pattern, $subject) === 1;
        }, $subject);
    }

    /**
     * Perform a preg match all, wrapping up the result.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<int, string>
     */
    public static function occurrences(string $pattern, string $subject)
    {
        return self::pregAndWrap(static function (string $subject) use ($pattern) {
            return (int) @\preg_match_all($pattern, $subject);
        }, $subject);
    }

    /**
     * Perform a preg replace callback, wrapping up the result.
     *
     * @param string                     $pattern
     * @param callable(string[]): string $callback
     * @param string                     $subject
     * @param int|null                   $limit
     *
     * @return \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<string, string>
     */
    public static function replaceCallback(string $pattern, callable $callback, string $subject, ?int $limit = null)
    {
        return self::pregAndWrap(static function (string $subject) use ($pattern, $callback, $limit) {
            return (string) @\preg_replace_callback($pattern, $callback, $subject, $limit ?? -1);
        }, $subject);
    }

    /**
     * Perform a preg split, wrapping up the result.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<string[], string>
     */
    public static function split(string $pattern, string $subject)
    {
        return self::pregAndWrap(static function (string $subject) use ($pattern) {
            /** @var string[] */
            return (array) @\preg_split($pattern, $subject);
        }, $subject);
    }

    /**
     * Perform a preg operation, wrapping up the result.
     *
     * @template V
     *
     * @param callable(string): V $operation
     * @param string              $subject
     *
     * @return \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<V, string>
     */
    private static function pregAndWrap(callable $operation, string $subject)
    {
        $result = $operation($subject);

        if (\preg_last_error() !== \PREG_NO_ERROR) {
            /** @var \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<V,string> */
            return Error::create(\preg_last_error_msg());
        }

        /** @var \KadenceWP\KadenceStarterTemplates\GrahamCampbell\ResultType\Result<V,string> */
        return Success::create($result);
    }
}
