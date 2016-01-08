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
    protected $recent_html_history;

    /**
     * @var VersionHistory\ByTypeAndDate
     */
    protected $recent_dep_history;

    /**
     * @var VersionHistory\ByTypeAndDate
     */
    protected $year_html_history;

    /**
     * @var VersionHistory\ByTypeAndDate
     */
    protected $year_dep_history;

    function __construct($options = array())
    {
        $this->options += $options;
        
        if (!isset($this->options['after_date'])) {
            $this->after_date = date("Y-m-d", strtotime('1 year ago midnight'));
        } else {
            $this->after_date = $this->options['after_date'];
        }
        
        $last_7_days = array();
        for ($i=0; $i<14; $i++) {
            $last_7_days[] = date("Y-m-d", strtotime($i." days ago"));
        }
        
        $last_year = array();
        for ($i = 1; $i <= 56; $i++) {
            $last_year[] = date("Y-m-d", strtotime("$i weeks ago"));
        }
        
        $this->recent_html_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_HTML,
            'dates' => $last_7_days
        ));

        $this->recent_dep_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_DEP,
            'dates' => $last_7_days
        ));
        
        $this->year_html_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_HTML,
            'dates' => $last_year
        ));
        
        $this->year_dep_history = new VersionHistory\ByTypeAndDate(array(
            'version_type' => VersionHistory::VERSION_TYPE_DEP,
            'dates' => $last_year
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

    public function getVersionHelper()
    {
        return new FrameworkVersionHelper();
    }
    
    public function getRecentHTMLGraph()
    {
        $data = $this->getGraphData(VersionHistory::VERSION_TYPE_HTML, $this->recent_html_history);
        return new VersionGraph(VersionHistory::VERSION_TYPE_HTML, $data);
    }

    public function getRecentDepGraph()
    {
        $data =  $this->getGraphData(VersionHistory::VERSION_TYPE_DEP, $this->recent_dep_history);
        return new VersionGraph(VersionHistory::VERSION_TYPE_DEP, $data);
    }

    public function getYearHTMLGraph()
    {
        $data =  $this->getGraphData(VersionHistory::VERSION_TYPE_HTML, $this->year_html_history);
        return new VersionGraph(VersionHistory::VERSION_TYPE_HTML, $data);
    }

    public function getYearDepGraph()
    {
        $data =  $this->getGraphData(VersionHistory::VERSION_TYPE_DEP, $this->year_dep_history);
        return new VersionGraph(VersionHistory::VERSION_TYPE_DEP, $data);
    }
    
    
    public function getGraphData($type, $data)
    {
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
        $hex = dechex(crc32($str));
        $hex = substr($hex, 0, 6);

        $r = hexdec(substr($hex,0,2));
        $g = hexdec(substr($hex,2,2));
        $b = hexdec(substr($hex,4,2));

        $rgb = array($r, $g, $b);
        return implode(",", $rgb);
    }
}
