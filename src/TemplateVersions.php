<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Config;

class TemplateVersions
{
    public $options = array(
        'current_branches' => array('master', '3.1'), //The currently supported branches in github
        'cache' => true, //cache the results
        'autoload' => true, //autoload the current versions
    );
    
    protected $cache_file = false;
    protected $versions = array('html'=>array(), 'dep'=>array());
    
    const VERSION_NAME_DEP = 'dep';
    const VERSION_NAME_HTML = 'html';
    
    public function __construct(array $options = array())
    {
        $this->options = $options + $this->options;
        
        $this->cache_file = Config::get('CACHE_DIR') . 'unl_template_versions.json';
        
        if ($this->options['autoload']) {
            $this->versions = $this->get($this->options['cache']);
        }
    }

    /**
     * get the current versions
     * 
     * will return something similar to 
     * array('html'=>array(), 'dep'=>array())
     * 
     * @return array the versions array
     */
    public function getVersions()
    {
        return $this->versions;
    }

    /**
     * Set the versions array
     * 
     * a valid versions array is of the form
     * 
     * array('html'=>array(), 'dep'=>array())
     * 
     * @param array $versions the versions array
     * @return null
     */
    public function setVersions(array $versions)
    {
        $this->versions = $versions;
    }

    /**
     * grabs the current versions for cache or github the versions array.  Will generate cache if $cache is true
     * 
     * @param bool $cache - retrieve from and save to cache
     * @return array|false
     */
    public function grabVersions($cache = true)
    {
        if ($cache) {
            if ($data = $this->getFromCache()) {
                return $data;
            }
        }

        $versions = array(
            'html' => array(),
            'dep' => array()
        );
        
        foreach ($this->options['current_branches'] as $branch) {
            $html_version = @file_get_contents('https://raw.github.com/unl/wdntemplates/' . $branch . '/VERSION_HTML');
            $dep_version = @file_get_contents('https://raw.github.com/unl/wdntemplates/' . $branch . '/VERSION_DEP');

            if ($html_version = $this->parseVersionFile($html_version)) {
                $versions['html'][] = $html_version;
            }

            if ($dep_version = $this->parseVersionFile($dep_version)) {
                $versions['dep'][] = $dep_version;
            }
        }
        
        if ($cache) {
            $this->setCache($versions);
        }
        
        return $versions;
    }

    /**
     * Parse the contents of a version file 
     * 
     * @param $contents
     * @return false|string the version
     */
    public function parseVersionFile($contents)
    {
        preg_match('/([0-9.]*)/', $contents, $matches);

        if (isset($matches[1])) {
            return $matches[1];
        }

        return false;
    }

    /**
     * Get the versions array from cache
     * 
     * @return false|array
     */
    public function getFromCache()
    {
        if ($json = @file_get_contents($this->cache_file)) {
            return json_decode($json, true);
        }
        
        return false;
    }

    /**
     * Save a versions array to cache
     * 
     * @param array $versions the versions array
     * @return int
     */
    public function setCache(array $versions)
    {
        return file_put_contents($this->cache_file, json_encode($versions));
    }

    /**
     * @param string $version the version to check
     * @param  string $version_name one of 'dep' or 'html'
     * @return bool
     */
    public function isCurrent($version, $version_name)
    {
        if (!in_array($version_name, array(self::VERSION_NAME_DEP, self::VERSION_NAME_HTML))) {
            //Not a valid version name
            return false;
        }
        
        if (!isset($this->versions[$version_name])) {
            return false;
        }
        
        if (in_array($version, $this->versions[$version_name])) {
            //if it is in the array of versions for this version_name, it is current
            return true;
        }
        
        return false;
    }
}