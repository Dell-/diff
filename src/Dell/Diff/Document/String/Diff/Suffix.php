<?php
namespace Dell\Diff\Document\String\Diff;

/**
 * Class Suffix
 */
class Suffix
{
    /**
     * @param string $lFragment
     * @param string $rFragment
     * @return int
     */
    private function length($lFragment, $rFragment)
    {
        $lLength = mb_strlen($lFragment);
        $rLength = mb_strlen($rFragment);
        if (!$lLength
            || !$rLength
            || mb_substr($lFragment, $lLength - 1) !== mb_substr($rFragment, $rLength - 1)
        ) {
            return 0;
        }

        $min = 0;
        $end = 0;
        $max = min($lLength, $rLength);
        $position = $end;

        while ($min < $position) {

            if (mb_substr($lFragment, $lLength - $position, $lLength - $end)
                === mb_substr($rFragment, $rLength - $position, $rLength - $end)
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
     * @param string $lFragment
     * @param string $rFragment
     * @return array
     */
    public function create($lFragment, $rFragment)
    {
        $length = $this->length($lFragment, $rFragment);

        $lLength = mb_strlen($lFragment);
        $rLength = mb_strlen($rFragment);

        return [
            mb_substr($lFragment, mb_strlen($lFragment) - $length), // suffix
            mb_substr($lFragment, $lLength - $length), // left fragment
            mb_substr($rFragment, $rLength - $length) // right fragment
        ];
    }
}
