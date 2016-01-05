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
    protected $sites = array();
    
    protected $version;

    protected $version_type;

    function __construct($options = array())
    {
        $this->options += $options;
        
        if (isset($options['vhtml'])) {
            $this->version = $options['vhtml'];
            $this->version_type = SitesInVersion::VERSION_TYPE_HTML;
        } else if (isset($options['vdep'])) {
            $this->version = $options['vdep'];
            $this->version_type = SitesInVersion::VERSION_TYPE_DEP;
        }
        
        if (!empty($this->version)) {
            $this->sites = new SitesInVersion(array(
                'version' => $this->version,
                'version_type' => $this->version_type
            ));
        }
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
        $title = 'Sites In Version';
        
        if (!empty($this->version)) {
            $title .= ': ' . $this->version;
        }
        
        return $title;
    }
    
    public function getSites()
    {
        return $this->sites;
    }
    
    public function getVersion()
    {
        return $this->version;
    }
    
    public function getVersionHelper()
    {
        return new FrameworkVersionHelper();
    }
}
