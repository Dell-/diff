<?php
namespace Dell\Diff\Document\String;
use Dell\Diff\Document\String\Diff\Prefix;
use Dell\Diff\Document\String\Diff\Suffix;

/**
 * Class Match
 */
class Match
{
    const DIFF_EQUAL = 0;
    const DIFF_INSERT = 1;
    const DIFF_DELETE = -1;

    /**
     * @param string $str1
     * @param string $str2
     * @return array
     */
    public function diff($str1, $str2)
    {
        $diffs = [];

        if ($str1 === null || $str2 === null) {
            throw new \InvalidArgumentException();
        }

        if (strcmp($str1, $str2) === 0) {
            if (!empty($str1)) {
                return [[self::DIFF_EQUAL, $str1]];
            }

            return [];
        }

        // Trim off common suffix
        list($suffix, $str1, $str2) = (new Suffix())->create($str1, $str2);

        // Trim off common prefix
        list($prefix, $str1, $str2) = (new Prefix())->create($str1, $str2);



        return $diffs;
    }
}
