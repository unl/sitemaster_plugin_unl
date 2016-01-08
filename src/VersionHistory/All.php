<?php
namespace SiteMaster\Plugins\Unl\VersionHistory;

use DB\RecordList;
use SiteMaster\Core\InvalidArgumentException;

class All extends RecordList
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
        $options['itemClass'] = '\SiteMaster\Plugins\Unl\VersionHistory';
        $options['listClass'] = __CLASS__;

        return $options;
    }

    public function getSQL()
    {
        //Build the list
        $sql = "SELECT id
                FROM unl_version_history";
        
        $sql .= "\n ORDER BY date_created ASC";
        
        if ($this->options['sql_limit']) {
            $sql .= "\n LIMIT " . (int)$this->options['sql_limit'];
        }

        return $sql;
    }
}
