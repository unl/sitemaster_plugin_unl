<?php
namespace SiteMaster\Plugins\Unl;

use DB\Record;
use DB\RecordList;
use SiteMaster\Core\Registry\Site\Member;
use Sitemaster\Core\Util;

class VersionHistory extends Record
{
    public $id; //int required
    public $version_type; //ENUM('HTML', 'DEP')
    public $version_number; //VARCHAR
    public $number_of_sites; //INT
    public $date_created; //DATETIME
    
    const VERSION_TYPE_HTML = 'HTML';
    const VERSION_TYPE_DEP = 'DEP';

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_version_history';
    }

    /**
     * Get a VersionHistory object
     *
     * @param int $id
     * @return bool|VersionHistory
     */
    public static function getByID($id)
    {
        return self::getByAnyField(__CLASS__, 'id', $id);
    }

    /**
     * Get a VersionHistory object
     *
     * @param $date
     * @param $version_type
     * @param $version
     * @return bool|VersionHistory
     */
    public static function getByDateAndVersion($date, $version_type, $version)
    {
        $version_sql = 'version_number = "' . RecordList::escapeString($version) . '"';
        if (null === $version) {
            $version_sql = 'version_number IS NULL';
        }
        
        return self::getByAnyField(
            __CLASS__, 
            'date_created',
            $date,
            'version_type = "' . RecordList::escapeString($version_type) . '" AND ' . $version_sql
        );
    }

    /**
     * Create a new Version History Record
     *
     * @param $version_type
     * @param $version_number
     * @param $number_of_sites
     * @param $date_created
     * @return bool|ScanAttributes
     */
    public static function createHistoryRecord($version_type, $version_number, $number_of_sites, $date_created)
    {
        $record = new self();

        $record->version_type = $version_type;
        $record->version_number = $version_number;
        $record->number_of_sites = $number_of_sites;
        $record->date_created = $date_created;

        if (!$record->insert()) {
            return false;
        }

        return $record;
    }
}
