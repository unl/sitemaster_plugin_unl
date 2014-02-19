<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Config;

class TemplateVersions
{
    public $options = array(
        'current_branches' => array('master', '3.1'), //The currently supported branches in github
    );
    
    public $cache_file = false;
    
    public function __construct($options = array())
    {
        $this->options = $this->options + $options;
        
        $this->cache_file = Config::get('CACHE_DIR') . 'unl_template_versions.json';
    }

    /**
     * Get the versions array.  Will generate cache if $cache is true
     * 
     * @param bool $cache - retrieve from and save to cache
     * @return array|false
     */
    public function get($cache = true)
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
    
}