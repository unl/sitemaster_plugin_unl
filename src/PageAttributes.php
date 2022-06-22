<?php
namespace SiteMaster\Plugins\Unl;

use DB\Record;
use SiteMaster\Core\Registry\Site\Member;
use Sitemaster\Core\Util;

class PageAttributes extends Record
{
    public $id;               //int required
    public $scanned_page_id;  //fk for scanned_page.id
    public $html_version;     //VARCHAR(10)
    public $dep_version;      //VARCHAR(10)
    public $template_type;    //VARCHAR(20)

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_page_attributes';
    }

    /**
     * Get a PageAttributes object for a scan
     *
     * @param int $scanned_page_id the scanned_page.id
     * @return bool|ScanAttributes
     */
    public static function getByScannedPageID($scanned_page_id)
    {
        return self::getByAnyField(__CLASS__, 'scanned_page_id', $scanned_page_id);
    }

    /**
     * Create a new Page Attributes
     *
     * @param int $scanned_page_id fk for scanned_page.id
     * @param string $html_version the html version of the page
     * @param string $dep_version the dependents version of the page
     * @param array $fields an associative array of field names and values to insert
     * @return bool|PageAttributes
     */
    public static function createPageAttributes($scanned_page_id, $html_version, $dep_version, $template_type, array $fields = array())
    {
        $link = new self();
        $link->synchronizeWithArray($fields);

        $link->scanned_page_id = $scanned_page_id;
        $link->html_version    = $html_version;
        $link->dep_version     = $dep_version;
        $link->template_type   = $template_type;

        if (!$link->insert()) {
            return false;
        }

        return $link;
    }
}
