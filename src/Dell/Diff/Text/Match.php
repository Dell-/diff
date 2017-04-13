<?php
namespace Dell\Diff\Text;

use Dell\Diff\Text\Diff\Equal;
use Dell\Diff\Text\Match\Prefix;
use Dell\Diff\Text\Match\Suffix;

/**
 * Class Match
 */
class Match
{
    /**
     * @var array
     */
    private $diffs;

    /**
     * @param Fragment $lFragment
     * @param Fragment $rFragment
     * @return Equal
     */
    public function diff(Fragment $lFragment, Fragment $rFragment)
    {
        if ($lFragment->equals($rFragment)) {
            return new Equal($lFragment);
        }

        $prefix = new Prefix();
        $suffix = new Suffix();

        list($prefixString, $tLFragment, $tRFragment) = $prefix->create($lFragment, $lFragment);

        list($suffixString, $tLFragment, $tRFragment) = $suffix->create($tLFragment, $tRFragment);
    }
}
