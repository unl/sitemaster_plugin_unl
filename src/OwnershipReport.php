<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Plugins\Unl\CMSID\All as CMSID_All;
use SiteMaster\Core\Registry\Site\Members\All as Members_All;

use SiteMaster\Core\ViewableInterface;

class OwnershipReport implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    public $sites = array();

    // This page is for displaying all CMS sites along with their ownership/management
    function __construct($options = array())
    {
        $this->options += $options;

        $this->sites = new CMSID_All();
    }

    public function getURL()
    {
        return \SiteMaster\Core\Config::get('URL') . 'unl_site_ownership/';
    }

    public function getPageTitle()
    {
        return 'Ownership Report';
    }

    public function getMembers($site_id)
    {
        return new Members_All(array('site_id' => $site_id));
    }
}
