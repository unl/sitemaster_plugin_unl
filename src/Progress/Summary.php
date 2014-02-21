<?php
namespace SiteMaster\Plugins\Unl\Progress;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;
use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Plugins\Unl\Progress;

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
    
    public $progress = false;

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
    }
}
