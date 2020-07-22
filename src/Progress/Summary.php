<?php
namespace SiteMaster\Plugins\Unl\Progress;

use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;
use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Plugins\Unl\Progress;
use SiteMaster\Plugins\Unl\ScanAttributes;

class Summary
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var \SiteMaster\Core\Registry\Site
     */
    public $site = false;

    /**
     * @var bool|\SiteMaster\Plugins\Unl\PageAttributes
     */
    public $progress = false;

    /**
     * @var bool|\SiteMaster\Plugins\Unl\ScanAttributes
     */
    public $scan_attributes = false;

    /**
     * @var bool|\SiteMaster\Core\Auditor\Scan
     */
    public $scan = false;

    function __construct($options = array())
    {
        $this->options += $options;

        //get the site
        if (!isset($this->options['sites_id'])) {
            throw new InvalidArgumentException('a sites_id is required', 400);
        }

        if (!$this->site = Site::getByID($this->options['sites_id'])) {
            throw new InvalidArgumentException('Could not find that site', 400);
        }
        
        if (!$this->progress = Progress::getBySitesID($this->site->id)) {
            $this->progress = Progress::createNewProgress($this->site->id);
        }
        
        if ($this->scan = $this->site->getLatestScan()) {
            $this->scan_attributes = ScanAttributes::getByScansID($this->scan->id);
        }
    }

    /**
     * Determine if the found scan html version is valid (in 5.2)
     * 
     * @return bool
     */
    public function htmlIsValid()
    {
        if (!$this->scan_attributes) {
            return false;
        }

        return $this->versionIsValid($this->scan_attributes->html_version);
    }

    /**
     * Determine if the found scan dependents are valid (in 5.2)
     * 
     * @return bool
     */
    public function depIsValid()
    {
        if (!$this->scan_attributes) {
            return false;
        }
        
        return $this->versionIsValid($this->scan_attributes->dep_version);
    }

    /**
     * Determine if a given version is valid (in 5.2)
     * 
     * @param mixed $version the version to check
     * @return bool
     */
    public function versionIsValid($version)
    {
        if (version_compare($version, 5.2, '>=')) {
            return true;
        }

        return false;
    }
}
