<?php
namespace SiteMaster\Plugins\Unl;

use DB\Record;
use SiteMaster\Core\Registry\Site\Member;
use Sitemaster\Core\Util;

class ScanAttributes extends Record
{
    public $id;               //int required
    public $scans_id;         //fk for scans.id
    public $html_version;     //VARCHAR(10)
    public $dep_version;      //VARCHAR(10)

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_scan_attributes';
    }
    
    /**
     * Get a ScanAttributes object for a scan
     *
     * @param int $scans_id
     * @return bool|ScanAttributes
     */
    public static function getByScansID($scans_id)
    {
        return self::getByAnyField(__CLASS__, 'scans_id', $scans_id);
    }

    /**
     * Create a new Scan Attributes
     *
     * @param int $scans_id the scans.id
     * @param string $html_version the lowest html version found in the scan
     * @param string $dep_version the lowest dependants version found in the scan
     * @param array $fields an associative array of field names and values to insert
     * @return bool|ScanAttributes
     */
    public static function createScanAttributes($scans_id, $html_version, $dep_version, array $fields = array())
    {
        $link = new self();
        $link->synchronizeWithArray($fields);

        $link->scans_id = $scans_id;
        $link->html_version    = $html_version;
        $link->dep_version     = $dep_version;

        if (!$link->insert()) {
            return false;
        }

        return $link;
    }
}
