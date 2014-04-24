<?php
namespace SiteMaster\Plugins\Unl\Help;

class Notice
{
    /**
     * @var array
     */
    public $options = array();

    function __construct($options = array())
    {
        $this->options += $options;
    }
}
