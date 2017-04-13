<?php
namespace Dell\Diff\Text\Diff;

use Dell\Diff\Text\DiffInterface;
use Dell\Diff\Text\Fragment;

/**
 * Class Delete
 */
class Delete implements DiffInterface
{
    const CODE = -1;

    /**
     * @var Fragment
     */
    private $fragment;

    /**
     * Equal constructor.
     *
     * @param Fragment $fragment
     */
    public function __construct(Fragment $fragment)
    {
        $this->fragment = $fragment;
    }
}
