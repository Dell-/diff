<?php
namespace Dell\Diff\Document\String\Diff;

/**
 * Class Prefix
 */
class Prefix
{
    /**
     * @param string $lFragment
     * @param string $rFragment
     * @return int
     */
    private function length($lFragment, $rFragment)
    {
        if (empty($lFragment)
            || empty($rFragment)
            || mb_substr($lFragment, 0, 1) !== mb_substr($rFragment, 0, 1)
        ) {
            return 0;
        }

        $min = 0;
        $start = 0;
        $max = min(mb_strlen($lFragment), mb_strlen($rFragment));
        $position = $max;

        while ($min < $position) {

            if (mb_substr($lFragment, $start, $position) === mb_substr($rFragment, $start, $position)) {
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
     * @param string $lFragment
     * @param string $rFragment
     * @return array
     */
    public function create($lFragment, $rFragment)
    {
        $length = $this->length($lFragment, $rFragment);

        return [
            mb_substr($lFragment, 0, $length), // prefix
            mb_substr($lFragment, $length), // left fragment
            mb_substr($rFragment, $length) // right fragment
        ];
    }
}
