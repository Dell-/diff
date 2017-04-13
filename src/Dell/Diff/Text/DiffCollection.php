<?php
namespace Dell\Diff\Text;

/**
 * Class DiffCollection
 */
class DiffCollection
{
    /**
     * @var DiffInterface[]
     */
    public $diffs = [];

    public function add(DiffInterface $diff)
    {
        $this->diffs[] = $diff;
    }
}
