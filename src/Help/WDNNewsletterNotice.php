<?php
namespace SiteMaster\Plugins\Unl\Help;

class WDNNewsletterNotice
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
