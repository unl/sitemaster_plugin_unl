<?php
namespace SiteMaster\Plugins\Unl;

use DB\RecordList;
use RegExpRouter\Exception;
use SiteMaster\Core\Registry\Site;

class SitesInVersion extends RecordList
{
    const VERSION_TYPE_HTML = 'html';
    const VERSION_TYPE_DEP = 'dep';
    
    protected $version;
    protected $version_type;
    protected $production_status;
    
    public function __construct(array $options = array())
    {
        if (!isset($options['version'])) {
            throw new Exception('A version is required');
        }

        if (!isset($options['version_type'])) {
            throw new Exception('A version_type is required');
        }
        
        if (!isset($options['production_status'])) {
            $options['production_status'] = NULL;
        }

        $this->version = $options['version'];
        $this->version_type = $options['version_type'];
        $this->production_status = $options['production_status'];
        
        
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
        $sql = "SELECT sites.id, MAX(scans.id)
                FROM sites
                    LEFT JOIN scans ON (sites.id = scans.sites_id)
                    JOIN unl_scan_attributes ON (unl_scan_attributes.scans_id = scans.id) ";
        
        if ($this->version == 'none') {
            $sql .= "WHERE unl_scan_attributes.dep_version IS NULL";
        } else if (self::VERSION_TYPE_HTML == $this->version_type) {
            if ('none' == $this->version) {
                $sql .= "WHERE unl_scan_attributes.html_version IS NULL";
            } else {
                $sql .= "WHERE unl_scan_attributes.html_version = '" . $this->escapeString($this->version) . "'
                     AND unl_scan_attributes.dep_version >= '" . $this->escapeString($this->version) . "'";
            }
            
        } else {
            if ('none' == $this->version) {
                $sql .= "WHERE unl_scan_attributes.dep_version IS NULL";
            } else {
                $sql .= "WHERE unl_scan_attributes.dep_version = '" . $this->escapeString($this->version) . "'";
            }
        }
        
        if ($this->production_status) {
            $sql .= " AND sites.production_status = '" . $this->escapeString($this->production_status) . "'";
        }
        
        $sql .= " GROUP BY sites.id
                ORDER BY sites.base_url ASC";

        return $sql;
    }
}
