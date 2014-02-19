<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Auditor\Logger\Metrics;
use SiteMaster\Core\Auditor\Metric\Mark;
use SiteMaster\Core\Auditor\MetricInterface;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Core\Util;

class Metric extends MetricInterface
{
    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_merge_recursive($options, array(
            'message_text' => array(),
            'help_text' => array(),
        ));

        parent::__construct($plugin_name, $options);
    }

    /**
     * Get the human readable name of this metric
     *
     * @return string The human readable name of the metric
     */
    public function getName()
    {
        return 'UNL WDN';
    }

    /**
     * Get the Machine name of this metric
     *
     * This is what defines this metric in the database
     *
     * @return string The unique string name of this metric
     */
    public function getMachineName()
    {
        return 'unl_wdn';
    }

    /**
     * Determine if this metric should be graded as pass-fail
     *
     * @return bool True if pass-fail, False if normally graded
     */
    public function isPassFail()
    {
        return false;
    }

    /**
     * Scan a given URI and apply all marks to it.
     *
     * All that this
     *
     * @param string $uri The uri to scan
     * @param \DOMXPath $xpath The xpath of the uri
     * @param int $depth The current depth of the scan
     * @param \SiteMaster\Core\Auditor\Site\Page $page The current page to scan
     * @param \SiteMaster\Core\Auditor\Logger\Metrics $context The logger class which calls this method, you can access the spider, page, and scan from this
     * @return bool True if there was a successful scan, false if not.  If false, the metric will be graded as incomplete
     */
    public function scan($uri, \DOMXPath $xpath, $depth, Page $page, Metrics $context)
    {
        $this->markPage($page, $xpath);

        return true;
    }

    /**
     * This method will find broken links and mark a page appropriately
     *
     * @param Page $page the page to mark
     * @param \DOMXPath $xpath
     */
    public function markPage(Page $page, \DOMXPath $xpath)
    {
        
    }

    /**
     * get the message for a mark
     *
     * @param string $machine_name the machine name of the mark
     * @return string
     */
    public function getMarkMessage($machine_name)
    {
        if (isset($this->options['message_text'][$machine_name])) {
            return $this->options['message_text'][$machine_name];
        }

        return 'General WDN error';
    }

    /**
     * get the help text to be used with a mark for a given machine name
     *
     * @param string $machine_name the machine name of the mark
     * @return string
     */
    public function getMarkHelpText($machine_name)
    {
        if (isset($this->options['help_text'][$machine_name])) {
            return $this->options['help_text'][$machine_name];
        }

        return 'Fix this problem';
    }
}