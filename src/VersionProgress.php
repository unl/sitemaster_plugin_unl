<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;

class VersionProgress implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array(
        'production_status' => NULL
    );

    /**
     * @var array|SitesInVersion
     */
    protected $sites = array();
    
    protected $version;

    protected $version_type;

    function __construct($options = array())
    {
        $this->options = $options += $this->options;
        
        if (isset($options['vhtml'])) {
            $this->version = $options['vhtml'];
            $this->version_type = SitesInVersion::VERSION_TYPE_HTML;
        } else if (isset($options['vdep'])) {
            $this->version = $options['vdep'];
            $this->version_type = SitesInVersion::VERSION_TYPE_DEP;
        }
        
        if (!empty($this->version)) {
            if (!$data = file(__DIR__ . '/../files/framework_audit.csv')) {
                throw new \Exception('No data found', 500);
            }

            $data = array_map('str_getcsv', $data);
            //Remove the first 4 rows (header data)
            array_shift($data);
            array_shift($data);
            array_shift($data);
            array_shift($data);
            
            $this->sites = array();
            $version_index = ($this->version_type == SitesInVersion::VERSION_TYPE_HTML)?2:3;
            $version_to_test = $this->version;
            if ('none' == $version_to_test) {
                $version_to_test = 'null';
            }
            foreach ($data as $row) {
                if ($row[$version_index] == $version_to_test) {
                    $this->sites[] = $row[0];
                }
            }
            
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
