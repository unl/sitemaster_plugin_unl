<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;

class Progress4x0 implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array|SitesIn4x0
     */
    public $sites = array();

    function __construct($options = array())
    {
        $this->options += $options;
        
        $this->sites = new SitesIn4x0();
    }

    /**
     * Get the url for this page
     *
     * @return bool|string
     */
    public function getURL()
    {
        return \SiteMaster\Core\Config::get('URL') . 'unl_progress/4x0/';
    }

    /**
     * Get the title for this page
     *
     * @return string
     */
    public function getPageTitle()
    {
        return 'Sites in 4.0';
    }
}
