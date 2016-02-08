<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Registry\Site;

class VersionGraph
{
    protected $version_type;

    protected $data;

    public function __construct($version_type, VersionHistory\ByTypeAndDate $data)
    {
        $this->version_type = $version_type;
        $this->data = $this->getGraphData($version_type, $data);
    }

    public function getData()
    {
        return $this->data;
    }
    
    public function getVersionType()
    {
        return $this->version_type;
    }


    protected function getVersionHelper()
    {
        return new FrameworkVersionHelper();
    }

    protected function getGraphData($type, $data)
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
        switch($str) {
            case '4.0':
                //this was resulting in a light yellow, which had a very low contrast
                $rgb = array(255, 205, 0);
                break;
            default: 
                $hex = dechex(crc32($str));
                $hex = substr($hex, 0, 6);
        
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
        
                $rgb = array($r, $g, $b);
        }
        return implode(",", $rgb);
    }
}
