<?php
namespace SiteMaster\Plugins\Unl;

use DB\Record;

class CMSID extends Record
{
    public $id;               //int required
    public $site_id;               //int required
    public $unlcms_site_id;        //VARCHAR(20)
    public $next_gen_cms_site_id; //VARCHAR(20)

    public function keys()
    {
        return array('id');
    }

    public static function getTable()
    {
        return 'unl_cms_id';
    }

    /**
     * Get CMS id by the sitemaster site id
     *
     * @param int $site_id the sitemaster site id
     * @return bool|CMSID
     */
    public static function getBySiteId($site_id)
    {
        return self::getByAnyField(__CLASS__, 'site_id', $site_id);
    }

    
    /**
     * Create a new CMS ID entry
     *
     * @param int $site_id site_id of the sitemaster site
     * @param string $unlcms_site_id unlcms site id
     * @param string $next_gen_cms_site_id next-gen cms site id
     * @param array $fields an associative array of field names and values to insert
     * @return bool|CMSID
     */
    public static function createCMSID($site_id, $unlcms_site_id, $next_gen_cms_site_id, array $fields = array())
    {
        $link = new self();
        $link->synchronizeWithArray($fields);

        $link->site_id = $site_id;
        $link->unlcms_site_id = $unlcms_site_id;
        $link->next_gen_cms_site_id = $next_gen_cms_site_id;

        if (!$link->insert()) {
            return false;
        }

        return $link;
    }
}
