<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;

/**
 * Class TextFormatting
 *
 * @package App\Helpers
 */
class TextFormatting
{
    /**
     * Convert selected array values into delimited string
     *
     * @param Collection $fullArray
     * @param array $selectedArrayKeys
     * @param string $delimiter
     *
     * @return string
     */
    public static function arraySelectedToCSV($fullArray, $selectedArrayKeys, $delimiter = ', ')
    {
        return implode($delimiter, $fullArray->only($selectedArrayKeys)->values()->toArray());
    }

    /**
     * Un-escape a string @see escapeSearch
     *
     * @param string $text
     * @return string
     */
    public static function unescapeSearch($text)
    {
        $text = htmlspecialchars_decode($text);
        $text = str_replace([
            '&#x27;',
            '&#x2F;',
            '&#x5C;'
        ], [
            '\'',
            '/',
            '\\'
        ], $text);
        return $text;
    }

    /**
     * Escape a string to proper search in database
     *
     * @param string $text
     * @return string
     */
    public static function escapeSearch($text)
    {
        $text = preg_replace('/[^[:print:]]/', '', $text);// Removing any non-visible char
        $text = htmlspecialchars($text);
        $text = str_replace([
            '\'',
            '/',
            '\\',
        ], [
            '&#x27;',
            '&#x2F;',
            '&#x5C;'
        ], $text);
        return $text;
    }

    /**
     * Create a random encrypted string
     *
     * @param int $length
     * @return string
     */
    public static function encryptedString($length = 10)
    {
        return md5(Str::random($length));
    }

    /**
     * @param Validator|ValidationException $validator
     * @return string
     * @throws \Exception
     */
    public static function getValidatorString($validator)
    {
        $messages = $validator instanceof ValidationException ?
            $validator->errors() :
            $validator->errors()->getMessages();
        return implode('<br>', array_map(function ($fieldError) {
            return implode('<br>', $fieldError);
        }, $messages));
    }

    /**
     * Convert apostrophes and quotation marks to entities
     * @param string $text
     * @return mixed
     */
    public static function sanitiseText($text)
    {
        return str_ireplace(["'", '"'], ["&apos;", "&quot;"], $text);
    }

    /**
     * @param array $data
     * @return array
     */
    public static function sanitiseRequest($data)
    {
        foreach ($data as $index => $value) {
            if (!is_array($value)) {
                $data[$index] = preg_replace('/[^[:print:]]/', '', $value);
            }
        }

        return $data;
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    public static function getExceptionInfo(\Exception $exception)
    {
        return $exception->getMessage() . "\n" .
            'Error occurred at line ' . $exception->getLine() . ' of file ' . $exception->getFile() . ".\n" .
            'Stack trace: ' . $exception->getTraceAsString();
    }

    /**
     * @param array $data
     * @return array
     */
    public static function redactedInput($data)
    {
        $inputRedacted = $data;

        array_walk_recursive(
            $inputRedacted,
            function (&$value, $key) {
                if ($key === 'CCNumber' || $key === 'plugin-get-card-number') {
                    $value = '....' . substr($value, -4) . ' (redacted)';
                }

                if ($key === 'CCCVC' || $key === 'customer_password') {
                    $value = '(redacted)';
                }
            }
        );

        return $inputRedacted;
    }

    /**
     * Clean strings removing invalid characters
     *
     * @param string $value
     * @param bool $numeric
     * @param string $extra
     * @return string|null
     */
    public static function cleanString($value, $numeric = false, $extra = '')
    {
        if ($numeric) {
            return preg_replace('/[^0-9' . $extra . ']/', '', $value);
        }

        return preg_replace('/[^a-zA-Z0-9' . $extra . ']/', '', $value);
    }

    /**
     * Convert DB stored from ^key1=value1^key2=value2 to array
     *
     * @param string $value
     * @return array
     */
    public static function convertDbStoredParams($value)
    {
        preg_match_all("~[\^]([^\^]+)=([^\^]*)~", $value, $valueArray);
        return array_combine($valueArray[1], $valueArray[2]);
    }

    /**
     * @param array $elements
     * @return array
     */
    public static function removeEmpty($elements)
    {
        return array_filter($elements, function ($value) {
            return !empty($value);
        });
    }

    /**
     * Create a simple and small trace for debugging
     *
     * @param \Exception $exception
     * @param bool $forceDebug
     * @return string[]
     */
    public static function getTrace($exception = null, $forceDebug = false)
    {
        if (config('app.debug') || $forceDebug) {
            $trace = [];
            // Show original error on top
            $trace[] = get_class($exception) . ' - ' . $exception->getMessage();
            foreach (empty($exception) ? debug_backtrace(2) : $exception->getTrace() as $traceItem) {
                $file = $traceItem['file'] ?? $traceItem['class'];
                $line = empty($traceItem['line']) ? '' : ":{$traceItem['line']}";
                if (strpos($file, '/vendor/') !== false) {//Don't show vendor files
                    continue;
                }

                $where = str_replace([app_path(), '.php'], '', $file);
                $trace[] = "{$where}@{$traceItem['function']}{$line}";
            }

            return ['trace' => $trace];
        }

        return [];
    }

    /**
     * Generate param string from array
     *
     * @param array $arguments
     * @param string $glue
     * @return string
     */
    public static function delimitArguments($arguments, $glue = '^')
    {
        return urldecode(http_build_query($arguments, '', $glue));
    }
}
