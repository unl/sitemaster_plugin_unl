<?php
namespace SiteMaster\Plugins\Unl\Help;

use SiteMaster\Core\Config;
use SiteMaster\Core\ViewableInterface;

class Progress4x0 implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    function __construct($options = array())
    {
        $this->options += $options;
    }

    public function getURL()
    {
        return Config::get('URL') . 'unl_progress/help/4.0_progress';
    }

    public function getPageTitle()
    {
        return 'Help: 4.0 Progress Reporting';
    }
}
