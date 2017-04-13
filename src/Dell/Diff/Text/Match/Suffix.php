<?php
namespace Dell\Diff\Text\Match;

use Dell\Diff\Text\Fragment;

/**
 * Class Suffix
 */
class Suffix
{
    /**
     * @param string $leftFragment
     * @param string $rightFragment
     * @return int
     */
    private function length($leftFragment, $rightFragment)
    {
        $lLength = mb_strlen($leftFragment);
        $rLength = mb_strlen($rightFragment);
        if (!$lLength
            || !$rLength
            || mb_substr($leftFragment, $lLength - 1) !== mb_substr($rightFragment, $rLength - 1)
        ) {
            return 0;
        }

        $min = 0;
        $end = 0;
        $max = min($lLength, $rLength);
        $position = $end;

        while ($min < $position) {

            if (mb_substr($leftFragment, $lLength - $position, $lLength - $end)
                    === mb_substr($rightFragment, $rLength - $position, $rLength - $end)
            ) {
                $min = $position;
                $end = $min;
            } else {
                $max = $position;
            }

            $position = floor(($max - $min) / 2 + $min);
        }

        return $position;
    }

    /**
     * @param Fragment $leftFragment
     * @param Fragment $rightFragment
     * @return Fragment[]
     */
    public function create(Fragment $leftFragment, Fragment $rightFragment)
    {
        $leftFragment = $leftFragment->__toString();
        $rightFragment = $rightFragment->__toString();

        $length = $this->length($leftFragment, $rightFragment);

        $lLength = mb_strlen($leftFragment);
        $rLength = mb_strlen($rightFragment);

        return [
            new Fragment(mb_substr($leftFragment, mb_strlen($leftFragment) - $length)),
            new Fragment(mb_substr($leftFragment, $lLength - $length)),
            new Fragment(mb_substr($rightFragment, $rLength - $length))
        ];
    }
}