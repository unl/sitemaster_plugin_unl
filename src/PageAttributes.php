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

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_page_attributes';
    }

    /**
     * Create a new Page Attributes
     *
     * @param int $scanned_page_id fk for scanned_page.id
     * @param string $html_version the html version of the page
     * @param string $dep_version the dependants version of the page
     * @param array $fields an associative array of field names and values to insert
     * @return bool|PageAttributes
     */
    public static function createPageAttributes($scanned_page_id, $html_version, $dep_version, array $fields = array())
    {
        $link = new self();
        $link->synchronizeWithArray($fields);

        $link->scanned_page_id = $scanned_page_id;
        $link->html_version    = $html_version;
        $link->dep_version     = $dep_version;

        if (!$link->insert()) {
            return false;
        }

        return $link;
    }
}
