<?php
namespace SiteMaster\Plugins\Unl\VersionHistory;

use DB\RecordList;
use RegExpRouter\Exception;
use SiteMaster\Core\InvalidArgumentException;

class ByTypeAndDate extends RecordList
{
    public function __construct(array $options = array())
    {
        $this->options = $options + $this->options;

        if (!isset($this->options['version_type'])) {
            throw new Exception('version_type is required', 500);
        }

        if (!isset($this->options['dates'])) {
            throw new Exception('dates is required', 500);
        }
        
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
        $dates = array();
        foreach ($this->options['dates'] as $date) {
            $dates[] = "'".self::escapeString($date)."'";
        }
        
        $dates = implode(', ',$dates);
        
        //Build the list
        $sql = "SELECT id
                FROM unl_version_history
                WHERE version_type = '" . self::escapeString($this->options['version_type']) ."'
                 AND date_created IN (" . $dates. ")";

        $sql .= "\n ORDER BY date_created ASC";

        if (isset($this->options['sql_limit'])) {
            $sql .= "\n LIMIT " . (int)$this->options['sql_limit'];
        }

        return $sql;
    }
}
