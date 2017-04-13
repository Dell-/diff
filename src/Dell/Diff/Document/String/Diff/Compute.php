<?php
namespace Dell\Diff\Document\String\Diff;

use Dell\Diff\Document\String\Match;

/**
 * Class Compute
 */
class Compute
{
    /**
     * @var Match
     */
    private $match;

    /**
     * @param string $lFragment
     * @param string $rFragment
     * @return array
     */
    public function difference($lFragment, $rFragment)
    {
        $diffs = [];

        if (empty($lFragment)) {
            // Just add some text
            return [[Match::DIFF_INSERT, $rFragment]];
        }

        if (empty($rFragment)) {
            // Just delete some text
            return [[Match::DIFF_DELETE, $lFragment]];
        }

        $lLength = mb_strlen($lFragment);
        $rLength = mb_strlen($rFragment);

        $longText = $lLength > $rLength ? $lFragment : $rFragment;
        $shortText = $lLength > $rLength ? $rFragment : $lFragment;

        $index = mb_strpos($longText, $shortText);

        if ($index !== -1) {
            $diffs = [
                [Match::DIFF_INSERT, mb_substr($longText, 0, $index)],
                [Match::DIFF_EQUAL, $shortText],
                [Match::DIFF_INSERT, mb_substr($longText, mb_strlen($shortText) + $index)],
            ];
            // Swap insertions for deletions if diff is reversed.
            if ($lLength > $rLength) {
                $diffs[0][0] = $diffs[2][0] = Match::DIFF_DELETE;
            }

            return $diffs;
        }

        if (mb_strlen($shortText) === 1) {
            // Single character string.
            // After the previous speedup, the character can't be an equality.
            return [[Match::DIFF_DELETE, $lLength], [Match::DIFF_INSERT, $rLength]];
        }

//        var hm = this.halfMatch_(text1, text2);
        $halfMatch = [];

        if (!empty($halfMatch)) {
            // A half-match was found, sort out the return data.
            $text1A = $halfMatch[0];
            $text1B = $halfMatch[1];
            $text2A = $halfMatch[2];
            $text2B = $halfMatch[3];
            $midCommon = $halfMatch[4];

            // Send both pairs off for separate processing.
            $diffsA = $this->match->diff($text1A, $text2A);
            $diffsB = $this->match->diff($text1B, $text2B);

            // Merge the results.
            return array_merge($diffsA, [[Match::DIFF_EQUAL, $midCommon]], $diffsB);
        }

        return $diffs;
    }

    /**
     * @param string $text1
     * @param string $text2
     */
    private function bisect($text1, $text2)
    {
        $text1Length = mb_strlen($text1);
        $text2Length = mb_strlen($text2);
        $maxD = ceil(($text1Length + $text2Length) / 2);
        $vOffset = (int)$maxD;
        $vLength = 2 * $maxD;
        $v1 = [];
        $v2 = [];

        // Setting all elements to -1 is faster in Chrome & Firefox than mixing
        // integers and undefined.
        for ($x = 0; $x < $vLength; ++$x) {
            $v1[$x] = -1;
            $v2[$x] = -1;
        }

        $v1[$vOffset + 1] = 0;
        $v2[$vOffset + 1] = 0;

        $delta = $text1Length - $text2Length;

        // If the total number of characters is odd, then the front path will collide
        // with the reverse path.
        $front = ($delta % 2 !== 0);

        // Offsets for start and end of k loop.
        // Prevents mapping of space beyond the grid.
        $k1start = 0;
        $k1end = 0;
        $k2start = 0;
        $k2end = 0;

        for ($d = 0; $d < $maxD; ++$d) {
            // Walk the front path one step.
            for ($k1 = -$d + $k1start; $k1 <= $d - $k1end; $k1 += 2) {
                $k1Offset = $vOffset + $k1;
                if ($k1 === -$d || ($k1 !== $d && $v1[$k1Offset - 1] < $v1[$k1Offset + 1])) {
                    $x1 = $v1[$k1Offset + 1];
                } else {
                    $x1 = $v1[$k1Offset - 1] + 1;
                }
                $y1 = $x1 - $k1;
                while ($x1 < $text1Length
                    && $y1 < $text2Length
                    && $text1[$x1] === $text2[$y1]
                ) {
                    ++$x1;
                    ++$y1;
                }
                $v1[$k1Offset] = $x1;
                if ($x1 > $text1Length) {
                    // Ran off the right of the graph.
                    $k1end += 2;
                } else if ($y1 > $text2Length) {
                    // Ran off the bottom of the graph.
                    $k1start += 2;
                } else if ($front) {
                    $k2Offset = $vOffset + $delta - $k1;
                    if ($k2Offset >= 0 && $k2Offset < $vLength && $v2[$k2Offset] != -1) {
                        // Mirror x2 onto top-left coordinate system.
                        $x2 = $text1Length - $v2[$k2Offset];
                        if ($x1 >= $x2) {
                            // Overlap detected.
                            return $this->bisectSplit($text1, $text2, $x1, $y1);
                        }
                    }
                }
            }

            // Walk the reverse path one step.
            for ($k2 = -$d + $k2start; $k2 <= $d - $k2end; $k2 += 2) {
                $k2Offset = $vOffset + $k2;
                if ($k2 == -$d || ($k2 != $d && $v2[$k2Offset - 1] < $v2[$k2Offset + 1])) {
                    $x2 = $v2[$k2Offset + 1];
                } else {
                    $x2 = $v2[$k2Offset - 1] + 1;
                }
                $y2 = $x2 - $k2;
                while ($x2 < $text1Length && $y2 < $text2Length &&
                    $text1[$text1Length - $x2 - 1] ===
                    $text2[$text2Length - $y2 - 1]) {
                    ++$x2;
                    ++$y2;
                }
                $v2[$k2Offset] = $x2;
                if ($x2 > $text1Length) {
                    // Ran off the left of the graph.
                    $k2end += 2;
                } else if ($y2 > $text2Length) {
                    // Ran off the top of the graph.
                    $k2start += 2;
                } else if (!$front) {
                    $k1Offset = $vOffset + $delta - $k2;
                    if ($k1Offset >= 0 && $k1Offset < $vLength && $v1[$k1Offset] != -1) {
                        $x1 = $v1[$k1Offset];
                        $y1 = $vOffset + $x1 - $k1Offset;
                        // Mirror x2 onto top-left coordinate system.
//                        x2 = text1_length - x2;
//                        if (x1 >= x2) {
//                            // Overlap detected.
//                            return this.bisectSplit_(text1, text2, x1, y1, deadline);
//                        }
                    }
                }
            }
        }
    }

    /**
     * @param string $text1
     * @param string $text2
     * @param int $x
     * @param int $y
     * @return array
     */
    private function bisectSplit($text1, $text2, $x, $y)
    {
        $text1a = mb_substr($text1, 0, $x);
        $text2a = mb_substr($text2,0, $y);

        $text1b = mb_substr($text1, $x);
        $text2b = mb_substr($text2, $y);

        // Compute both diffs serially.
        $diffs = $this->match->diff($text1a, $text2a);
        $diffsB = $this->match->diff($text1b, $text2b);

        return array_merge($diffs, $diffsB);
    }
}
