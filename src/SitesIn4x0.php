<?php
namespace SiteMaster\Plugins\Unl;

use DB\RecordList;

class SitesIn4x0 extends RecordList
{
    public function __construct(array $options = array())
    {
        $options['array'] = self::getBySQL(array(
            'sql'         => $this->getSQL(),
            'returnArray' => true
        ));

        parent::__construct($options);
    }

    public function getDefaultOptions()
    {
        $options = array();
        $options['itemClass'] = '\SiteMaster\Core\Registry\Site';
        $options['listClass'] = __CLASS__;

        return $options;
    }

    public function getSQL()
    {
        //Build the list
        $sql = "SELECT sites.id, MAX(scans.id)
                FROM sites
                    LEFT JOIN scans ON (sites.id = scans.sites_id)
                    JOIN unl_scan_attributes ON (unl_scan_attributes.scans_id = scans.id)
                    WHERE unl_scan_attributes.html_version >= '4.0'
                    AND unl_scan_attributes.dep_version >= '4.0'
                GROUP BY sites.id
                ORDER BY sites.base_url ASC";

        return $sql;
    }
}

