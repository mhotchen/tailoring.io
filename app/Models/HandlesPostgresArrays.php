<?php
namespace App\Models;

use MyCLabs\Enum\Enum;

/**
 * @mixin \Eloquent
 */
trait HandlesPostgresArrays
{
    /**
     * Influenced by https://stackoverflow.com/a/27964420
     *
     * This is a recursive function, ignore all parameters except the first one when calling it.
     *
     * I'm slightly worried about this function's handling of multi-byte characters but right now that isn't a
     * requirement.
     *
     * @param string   $string
     * @param int      $start  Ignore, internal use only
     * @param int|null $end    Ignore, internal use only
     * @return array
     * @throws \InvalidArgumentException
     */
    private function fromPostgresArray(string $string, int $start = 0, int &$end = null): array
    {
        if (empty($string) || $string[0] != '{') {
            throw new \InvalidArgumentException("String must be in valid postgres array format: '$string' given.");
        }

        $return = [];
        $isCurrentValueAString = false;
        $quote = '';
        $length = strlen($string);
        $currentValue = '';

        for ($i = $start + 1; $i < $length; $i++) {
            $ch = $string[$i];

            if (!$isCurrentValueAString && $ch === '}') {
                if ($currentValue !== '' || !empty($return)) {
                    $return[] = $currentValue;
                }

                $end = $i;
                break;
            } elseif (!$isCurrentValueAString && $ch === '{') {
                $currentValue = $this->fromPostgresArray($string, $i, $i);
            } elseif (!$isCurrentValueAString && $ch === ','){
                $return[] = $currentValue;
                $currentValue = '';
            } elseif (!$isCurrentValueAString && ($ch === '"' || $ch === "'")) {
                $isCurrentValueAString = true;
                $quote = $ch;
            } elseif ($isCurrentValueAString && $ch === $quote && $string[$i - 1] === "\\") {
                $currentValue = substr($currentValue, 0, -1) . $ch;
            } elseif ($isCurrentValueAString && $ch === $quote && $string[$i - 1] != "\\") {
                $isCurrentValueAString = false;
            } else {
                $currentValue .= $ch;
            }
        }

        return $return;
    }

    /**
     * Influenced by https://stackoverflow.com/a/44324426
     *
     * @todo This isn't currently recursive; maybe it doesn't need to be?
     *
     * @param iterable $data
     * @return string
     * @throws \InvalidArgumentException
     */
    private function toPostgresArray(iterable $data): string
    {
        $result = [];

        foreach ($data as $element) {
            if (is_iterable($element)) {
                $result[] = $this->toPostgresArray($element);
            } elseif ($element === null) {
                $result[] = 'NULL';
            } elseif ($element === true) {
                $result[] = 'TRUE';
            } elseif ($element === false) {
                $result[] = 'FALSE';
            } elseif (is_numeric($element)) {
                $result[] = $element;
            } elseif ($element instanceof Enum) {
                $result[] = $element;
            } elseif (is_string($element) || method_exists($element, '__toString')) {
                $result[] = $this
                    ->getConnection()
                    ->getPdo()
                    ->quote((string) $element);
            } else {
                throw new \InvalidArgumentException('Unsupported array item');
            }
        }

        return sprintf('{%s}', implode(',', $result));
    }
}