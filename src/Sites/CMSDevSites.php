<?php
namespace SiteMaster\Plugins\Unl\Sites;

use DB\RecordList;
use SiteMaster\Core\InvalidArgumentException;

class CMSDevSites extends RecordList
{
    public function __construct(array $options = array())
    {
        $this->options = $options + $this->options;

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
        $sql = "SELECT sites.id
                FROM sites
                WHERE base_url LIKE 'http://unlcms.unl.edu/%'
                ORDER BY sites.base_url ASC";

        return $sql;
    }
}
