<?php
namespace Dell\Diff\Text;

/**
 * Class Fragment
 */
class Fragment
{
    /**
     * @var Fragment
     */
    private $prefix;

    /**
     * @var Fragment
     */
    private $suffix;

    /**
     * @var string
     */
    private $string;

    /**
     * @var string
     */
    private $hash;

    /**
     * @var Fragment
     */
    private $fragment;

    /**
     * Fragment constructor.
     *
     * @param $string
     */
    public function __construct($string)
    {
        $this->string = $string;
        $this->hash = $this->hash($this->string);
        $this->fragment = $this;
    }

    /**
     * @param Fragment $fragment
     * @return bool
     */
    public function equals(Fragment $fragment)
    {
        return $this->prefix->equals($fragment->prefix)
            && $this->hash === $fragment->hash
            && $this->suffix->equals($fragment->suffix);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->prefix->__toString() . $this->string . $this->suffix->__toString();
    }

    /**
     * @param string $string
     * @return string
     */
    private function hash($string)
    {
        return hash('md5', $string);
    }
}