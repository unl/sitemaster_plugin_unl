<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\ViewableInterface;

class VersionReport implements ViewableInterface
{
    /**
     * @var array
     */
    public $options = array();

    /**
     * @var VersionHistory\ByTypeAndDate
     */
    protected $html_history;

    /**
     * @var VersionHistory\ByTypeAndDate
     */
    protected $dep_history;
    
    protected $after_date;

    function __construct($options = array())
    {
        $this->options += $options;
        
        if (!isset($this->options['after_date'])) {
            $this->after_date = date("Y-m-d", strtotime('1 year ago midnight'));
        } else {
            $this->after_date = $this->options['after_date'];
        }
        
        $this->html_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_HTML,
            'after_date' => $this->after_date
        ));

        $this->dep_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_DEP,
            'after_date' => $this->after_date
        ));
    }

    /**
     * Get the url for this page
     *
     * @return bool|string
     */
    public function getURL()
    {
        return \SiteMaster\Core\Config::get('URL') . 'unl_versions/';
    }

    /**
     * Get the title for this page
     *
     * @return string
     */
    public function getPageTitle()
    {
        $title = 'Framework Version Report';

        return $title;
    }

    public function getHTMLHistory()
    {
        return $this->html_history;
    }

    public function getDepHistory()
    {
        return $this->dep_history;
    }

    public function getVersionHelper()
    {
        return new FrameworkVersionHelper();
    }
    
    public function getGraphData($type)
    {
        if (VersionHistory::VERSION_TYPE_HTML == $type) {
            $data = $this->getHTMLHistory();
        } else {
            $data = $this->getDepHistory();
        }
        
        $dates = array();

        $version_helper = $this->getVersionHelper();

        $all_versions = $version_helper->getAllVersions();
        
        //Create an array of dates with counts for each version
        foreach ($data as $record) {
            if (!isset($dates[$record->date_created])) {
                //Create date and fill with default data
                $dates[$record->date_created] = array_fill_keys($all_versions[strtolower($type)], 0);
                $dates[$record->date_created]['unknown'] = 0;
            }
            
            $version_number = $record->version_number;
            
            if (!in_array($version_number, $all_versions[strtolower($type)])) {
                
                $version_number = 'unknown';
            }
            
            $dates[$record->date_created][$version_number] += $record->number_of_sites;
        }
        
        //Filter out zeros

        
        $data_sets = array();
        $data_sets['dates'] = array();
        
        foreach ($dates as $date=>$versions) {
            $data_sets['dates'][] = $date;
            foreach ($versions as $version=>$count) {
                if ($count > 0) {
                    //Only include versions for which we have results
                    $data_sets['versions'][$version][] = $count;
                }
            }
        }
        
        return $data_sets;
    }
    
    public static function stringToColorCode($str) {
        $code = dechex(crc32($str));
        $code = substr($code, 0, 6);
        return $code;
    }

    /**
     * @param $type
     * @return VersionGraph
     */
    public function getVersionGraph($type)
    {
        return new VersionGraph($type, $this->getGraphData($type));
    }
}
