<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;

class VersionProgress implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array|SitesInVersion
     */
    public $sites = array();

    function __construct($options = array())
    {
        $this->options += $options;
        
        $this->sites = new SitesInVersion();
    }

    /**
     * Get the url for this page
     *
     * @return bool|string
     */
    public function getURL()
    {
        return \SiteMaster\Core\Config::get('URL') . 'unl_progress/';
    }

    /**
     * Get the title for this page
     *
     * @return string
     */
    public function getPageTitle()
    {
        return 'Version Progress';
    }
}
