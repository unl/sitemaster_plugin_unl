<?php
namespace SiteMaster\Plugins\Unl\Site;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\InvalidArgumentException;
use SiteMaster\Core\ViewableInterface;

class DocumentList implements ViewableInterface
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
        if (!isset($this->options['site_id'])) {
            throw new InvalidArgumentException('a site_id is required', 400);
        }

        if (!$this->site = Site::getByID($this->options['site_id'])) {
            throw new InvalidArgumentException('Could not find that site', 400);
        }
    }

    public function get_base_url() {
        return $this->site->base_url;
    }

    public function getURL()
    {
        return $this->site->getURL() . 'unl/doc_summary/';
    }

    public function getPageTitle()
    {
        return 'Document Summary';
    }
}
