<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;

class VersionGraph
{
    protected $version_type;

    protected $data;

    public function __construct($version_type, $data)
    {
        $this->version_type = $version_type;
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function getVersionType()
    {
        return $this->version_type;
    }
}
