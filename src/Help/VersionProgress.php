<?php
namespace SiteMaster\Plugins\Unl\Help;

use SiteMaster\Core\Config;
use SiteMaster\Core\ViewableInterface;

class VersionProgress implements ViewableInterface
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
        return Config::get('URL') . 'unl_progress/help';
    }

    public function getPageTitle()
    {
        return 'Help: Progress Reporting';
    }
}
