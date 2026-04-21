<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Core\Registry\Site;

class DocumentSummary
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var \SiteMaster\Core\Registry\Site
     */
    public $site = false;

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
    }

    public function get_base_url() {
        return $this->site->base_url;
    }
}
