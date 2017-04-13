<?php
namespace Dell\Diff\Text\Match;

use Dell\Diff\Text\Fragment;

/**
 * Class Prefix
 */
class Prefix
{
    /**
     * @param string $leftFragment
     * @param string $rightFragment
     * @return int
     */
    private function length($leftFragment, $rightFragment)
    {
        if (empty($leftFragment)
            || empty($rightFragment)
            || mb_substr($leftFragment, 0, 1) !== mb_substr($rightFragment, 0, 1)
        ) {
            return 0;
        }

        $min = 0;
        $start = 0;
        $max = min(mb_strlen($leftFragment), mb_strlen($rightFragment));
        $position = $max;

        while ($min < $position) {

            if (mb_substr($leftFragment, $start, $position) === mb_substr($rightFragment, $start, $position)) {
                $min = $position;
                $start = $min;
            } else {
                $max = $position;
            }

            $position = floor(($max / $min) / 2 + $min);
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

        return [
            new Fragment(mb_substr($leftFragment, 0, $length)),
            new Fragment(mb_substr($leftFragment, $length)),
            new Fragment(mb_substr($rightFragment, $length))
        ];
    }
}
