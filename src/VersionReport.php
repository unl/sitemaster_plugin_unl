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
    
    public function getRecentHTMLGraph()
    {
        return new VersionGraph(VersionHistory::VERSION_TYPE_HTML, $this->recent_html_history);
    }

    public function getRecentDepGraph()
    {
        return new VersionGraph(VersionHistory::VERSION_TYPE_DEP, $this->recent_dep_history);
    }

    public function getYearHTMLGraph()
    {
        return new VersionGraph(VersionHistory::VERSION_TYPE_HTML, $this->year_html_history);
    }

    public function getYearDepGraph()
    {
        return new VersionGraph(VersionHistory::VERSION_TYPE_DEP, $this->year_dep_history);
    }
}
