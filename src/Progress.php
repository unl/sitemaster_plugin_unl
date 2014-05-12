<?php
namespace SiteMaster\Plugins\Unl;

use DB\Record;
use SiteMaster\Core\Registry\Site\Member;
use SiteMaster\Core\Util;

class Progress extends Record
{
    public $id;                       //int required
    public $sites_id;                 //fk for sites.id NOT NULL
    public $estimated_completion;     //DATE NOT NULL
    public $self_progress;            //INT(3) NOT NULL, zero to 100, default = 0
    public $self_comments;            //TEXT NULL
    public $created;                  //DATETIME NOT NULL
    public $updated;                  //DATETIME NOT NULL

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_site_progress';
    }

    /**
     * Get a Progress object for a site
     *
     * @param $sites_id
     * @return bool|ScanAttributes
     */
    public static function getBySitesID($sites_id)
    {
        return self::getByAnyField(__CLASS__, 'sites_id', $sites_id);
    }

    /**
     * Create a new Progress
     *
     * @param int $sites_id the sites.id to create a progress object for
     * @param array $fields an associative array of field names and values to insert
     * @return bool|PageAttributes
     */
    public static function createNewProgress($sites_id, array $fields = array())
    {
        $progress = new self();
        $progress->created              = Util::epochToDateTime();
        $progress->updated              = Util::epochToDateTime();
        $progress->estimated_completion = '2014-8-15';
        $progress->synchronizeWithArray($fields);

        $progress->sites_id      = $sites_id;
        $progress->self_progress = 0;

        if (!$progress->insert()) {
            return false;
        }

        return $progress;
    }
}
