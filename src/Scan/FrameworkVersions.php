<?php
namespace SiteMaster\Plugins\Unl\Scan;

use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Pages\AllForScan;
use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Core\ViewableInterface;

class FrameworkVersions implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var array
     */
    public $all_pages = array();

    /**
     * @var bool|\SiteMaster\Core\Auditor\Scan
     */
    public $scan = false;

    function __construct($options = array())
    {
        $this->options += $options;

        //get the site
        if (isset($this->options['scan'])) {
            $this->scan = $this->options['scan'];
        } else {
            //Try to get it by ID
            if (!isset($this->options['scans_id'])) {
                throw new InvalidArgumentException('a scan id is required', 400);
            }

            if (!$this->scan = Scan::getByID($this->options['scans_id'])) {
                throw new InvalidArgumentException('Could not find a scan for the given page.', 500);
            }
        }
        
        $this->all_pages = new AllForScan(array(
            'scans_id' => $this->scan->id
        ));
    }

    public function getURL()
    {
        return $this->scan->getURL() . 'unl/versions/';
    }

    public function getPageTitle()
    {
        return 'Framework Versions';
    }
}
