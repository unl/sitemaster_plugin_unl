<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Auditor\Logger\Metrics;
use SiteMaster\Core\Auditor\Metric\Mark;
use SiteMaster\Core\Auditor\MetricInterface;
use SiteMaster\Core\Config;
use SiteMaster\Core\Registry\Site;
use SiteMaster\Core\Auditor\Scan;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Core\Util;

class Metric extends MetricInterface
{
    const MARK_MN_UNL_FRAMEWORK_HTML = 'UNL_FRAMEWORK_HTML';
    const MARK_MN_UNL_FRAMEWORK_DEP = 'UNL_FRAMEWORK_DEP';
    const MARK_MN_UNL_FRAMEWORK_YOUTUBUE = 'UNL_FRAMEWORK_YOUTUBUE';
    const MARK_MN_UNL_FRAMEWORK_PDF_LINKS = 'UNL_FRAMEWORK_PDF';
    const MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT = 'UNL_FRAMEWORK_FLASH';
    const MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN = 'UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN';
    const MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS = 'UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS';
    const MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES = 'UNL_FRAMEWORK_BRAND_INCONSISTENCIES';
    
    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $options = array_replace_recursive(array(
            'title_text' => array(
                self::MARK_MN_UNL_FRAMEWORK_HTML => 'The UNLedu framework HTML is out of date',
                self::MARK_MN_UNL_FRAMEWORK_DEP => 'The UNLedu framework dependents are out of date',
                self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE => 'A Youtube Embed was found',
                self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS => 'A PDF was found. Please independently ensure PDF accessibility',
                self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT => 'A flash object was found',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => 'An icon font was found without aria-hidden="true"',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => 'An icon font is applied to an element with contents',
                self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES => 'Style inconsistencies were found with the University of Nebraska style guide.',
            ),
            'description_text' => array(
                self::MARK_MN_UNL_FRAMEWORK_HTML => 'The UNLedu framework HTML is out of date',
                self::MARK_MN_UNL_FRAMEWORK_DEP => 'The UNLedu framework dependents are out of date',
                self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE => 'It is important to keep in mind that youtube is blocked in some places around the world, including China.  It is a best practice to host video on mediahub.unl.edu, where the video will not be blocked.',
                self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS => 'Please ensure that the PDF is accessible.',
                self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT => 'The use of flash is discouraged as it does not work on most mobile devices',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => 'Screen readers might read icon-fonts and convey an incorrect or confusing meaning. Icon fonts should be hidden from screen readers.',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => 'Because icon fonts should be hidden from screen readers with aria-hidden="true", the element containing the icon font and text within it will not be read.',
                self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES => 'In written communication, the full name, University of Nebraska–Lincoln, should be spelled out when the university is first mentioned or cited. Thereafter, references should cite “the university” or “Nebraska.”',
            ),
            'help_text' => array(
                self::MARK_MN_UNL_FRAMEWORK_HTML => 'For mirroring instructions, see [Synchronizing the UNLedu Web Framework](http://wdn.unl.edu/synchronizing-unledu-web-framework)',
                self::MARK_MN_UNL_FRAMEWORK_DEP => 'For mirroring instructions, see [Synchronizing the UNLedu Web Framework](http://wdn.unl.edu/synchronizing-unledu-web-framework)',
                self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE => 'Host the video from [Mediahub](http://mediahub.unl.edu/)',
                self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS => 'See [webaim](http://webaim.org/techniques/acrobat/) for help with PDF accessibility.',
                self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT => 'Either remove the flash object, or replace it with an HTML5 alternative.',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => 'See [the WDN icon-font documentation](http://wdn.unl.edu/documentation/icons) for help with accessibility.',
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => 'See [the WDN icon-font documentation](http://wdn.unl.edu/documentation/icons) for help with accessibility.',
                self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES => 'See [the brand book](http://unlcms.unl.edu/ucomm/styleguide/u#UNL-abbrev) for more information on this topic.',
            ),
            'point_deductions' => array(
                self::MARK_MN_UNL_FRAMEWORK_HTML => 80,
                self::MARK_MN_UNL_FRAMEWORK_DEP => 20,
                self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE => 0,
                self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS => 0,
                self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT => 0,
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => 1,
                self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => 1,
                self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES => 0,
            )
        ), $options);

        parent::__construct($plugin_name, $options);
    }

    /**
     * Get the human readable name of this metric
     *
     * @return string The human readable name of the metric
     */
    public function getName()
    {
        return 'UNLedu Framework Checker';
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
        $this->markPage($page, $xpath, $context->getScan());

        return true;
    }

    /**
     * This method will find broken links and mark a page appropriately
     *
     * @param Page $page the page to mark
     * @param \DOMXPath $xpath
     * @param \SiteMaster\Core\Auditor\Scan $scan
     */
    public function markPage(Page $page, \DOMXPath $xpath, Scan $scan)
    {
        $html_version = $this->getHTMLVersion($xpath);
        $dep_version = $this->getDEPVersion($xpath);
        
        //Save these attributes for the page.
        PageAttributes::createPageAttributes($page->id, $html_version, $dep_version);
        
        if (!$scan_attributes = ScanAttributes::getByScansID($scan->id)) {
            $scan_attributes = ScanAttributes::createScanAttributes($scan->id, $html_version, $dep_version);
        } else {
            //Update the scan version if this page's versions are older
            if (version_compare($html_version, $scan_attributes->html_version) == -1) {
                //$html_version is smaller, so decrease the scan attribute version
                $scan_attributes->html_version = $html_version;
                $scan_attributes->save();
            }

            if (version_compare($dep_version, $scan_attributes->dep_version) == -1) {
                //$dep_version is smaller, so decrease the scan attribute version
                $scan_attributes->dep_version = $dep_version;
                $scan_attributes->save();
            }
            
            //Update the root site URL if we need to
            if (empty($scan_attributes->root_site_url) && $root = $this->getRootSiteURL($xpath)) {
                $scan_attributes->root_site_url = $root;
                $scan_attributes->save();
            }
        }
        
        $version_helper = new FrameworkVersionHelper();
        
        if (!$version_helper->isCurrent($html_version, FrameworkVersionHelper::VERSION_NAME_HTML)) {
            //Create a new mark
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_HTML;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );
            
            $page->addMark($mark, array(
                'value_found' => $html_version
            ));
        }

        if (!$version_helper->isCurrent($dep_version, FrameworkVersionHelper::VERSION_NAME_DEP)) {
            //Create a new mark
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_DEP;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );

            $page->addMark($mark, array(
                'value_found' => $dep_version
            ));
        }
        
        //youtube notice
        $embeds = $this->getYouTubeEmbeds($xpath);
        if (!empty($embeds)) {
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );
            
            foreach ($embeds as $embed) {
                $page->addMark($mark, array(
                    'value_found' => $embed
                ));
            }
        }
        
        $pdfs = $this->getPDFLinks($xpath);
        if (!empty($pdfs)) {
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );

            foreach ($pdfs as $pdf) {
                $page->addMark($mark, array(
                    'value_found' => $pdf['value_found'],
                    'context'     => $pdf['context']
                ));
            }
        }

        $flash_objects = $this->getFlashObjects($xpath);
        if (!empty($flash_objects)) {
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );

            foreach ($flash_objects as $flash_object) {
                $page->addMark($mark, array(
                    'value_found' => $flash_object['value_found'],
                    'context'     => $flash_object['context']
                ));
            }
        }

        $errors = $this->getIconFontErrors($xpath);
        foreach ($errors as $machine_name=>$elements) {
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );

            foreach ($elements as $element) {
                $page->addMark($mark, array(
                    'value_found' => $element['value_found'],
                    'context'     => $element['context']
                ));
            }
        }

        $errors = $this->getBrandInconsistencyReferences($xpath);
        if (!empty($errors)) {
            $machine_name = self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES;
            $mark = $this->getMark(
                $machine_name,
                $this->getMarkTitle($machine_name),
                $this->getMarkPointDeduction($machine_name),
                $this->getMarkDescription($machine_name),
                $this->getMarkHelpText($machine_name)
            );
            foreach ($errors as $error) {
                $page->addMark($mark, array(
                    'value_found' => $error['value_found'],
                    'context'     => $error['context']
                ));
            }
        }
    }

    /**
     * get the name for a mark
     *
     * @param string $machine_name the machine name of the mark
     * @return string
     */
    public function getMarkTitle($machine_name)
    {
        if (isset($this->options['title_text'][$machine_name])) {
            return $this->options['title_text'][$machine_name];
        }

        return 'Framework Error';
    }
    
    /**
     * get the point deduction for a mark
     *
     * @param string $machine_name the machine name of the mark
     * @return double
     */
    public function getMarkPointDeduction($machine_name)
    {
        if (isset($this->options['point_deductions'][$machine_name])) {
            return $this->options['point_deductions'][$machine_name];
        }

        return 0;
    }

    /**
     * get the message for a mark
     *
     * @param string $machine_name the machine name of the mark
     * @return string
     */
    public function getMarkDescription($machine_name)
    {
        if (isset($this->options['description_text'][$machine_name])) {
            return $this->options['description_text'][$machine_name];
        }

        return 'General UNLedu framework error';
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

    /**
     * Get the html version of a page
     * 
     * @param \DOMXPath $xpath the xpath
     * @return null|string the version (null if not found)
     */
    public function getHTMLVersion(\DOMXPath $xpath)
    {
        $version = '';

        //look for >= 3.1 templates
        $nodes = $xpath->query(
            '//xhtml:body/@data-version'
        );

        foreach ($nodes as $node) {
            $version = $node->nodeValue;
        }

        if (!empty($version)) {
            //found >= 3.1 templates
            return $version;
        }

        //Look for 3.0
        $nodes = $xpath->query(
            '//xhtml:script/@src'
        );

        foreach ($nodes as $node) {
            if (stripos($node->nodeValue, 'templates_3.0') !== false) {
                //Found 3.0
                return '3.0';
            }
        }

        //Couldn't find anything.
        return null;
    }

    /**
     * Get the dependency version of a page
     * 
     * @param \DOMXPath $xpath the xpath of the page
     * @return null|string the version (null if not found)
     */
    public function getDEPVersion(\DOMXPath $xpath)
    {
        $version = '';

        //look for >= 3.1 templates
        $nodes = $xpath->query(
            "//xhtml:script[@id='wdn_dependents']/@src"
        );

        foreach ($nodes as $node) {
            $version = $node->nodeValue;
        }

        $matches = array();

        if (preg_match('/all.js\?dep=([0-9.]*)/', $version, $matches) && isset($matches[1])) {
            //found look for >= 3.1 templates
            return $matches[1];
        }

        //look for 3.0
        $nodes = $xpath->query(
            '//xhtml:script/@src'
        );

        foreach ($nodes as $node) {
            if (stripos($node->nodeValue, 'templates_3.0') !== false) {
                //found 3.0
                return '3.0';
            }
        }

        //Couldn't find anything.
        return null;
    }

    /**
     * Determine if a youtube was embedded in the page
     * 
     * @param \DomXPath $xpath the xpath of the page
     * @return array an array of youtube embed sources will be returned
     */
    public function getYouTubeEmbeds(\DomXPath $xpath) {
        //look for youtubue embeds
        $nodes = $xpath->query(
            "//xhtml:iframe[contains(@src,'//www.youtube.com/embed/')]"
        );
        
        $sources = array();
        foreach ($nodes as $node) {
            $sources[] = $node->getAttribute('src');
        }
        
        return $sources;
    }

    /**
     * Get the root site for this page.  The root site is the first site found in the breadcrumbs, as long as it is not 'www.unl.edu'.
     * A root site is usually a college or department.
     * 
     * @param \DomXpath $xpath
     * @return bool
     */
    public function getRootSiteURL(\DomXpath $xpath)
    {
        //look for youtubue embeds
        $nodes = $xpath->query(
            "(//xhtml:*[@id='breadcrumbs']/xhtml:ul/xhtml:li|//xhtml:*[@id='breadcrumbs']/xhtml:ul/xhtml:li/xhtml:span)/xhtml:a"
        );
        
        switch ($nodes->length) {
            case 0:
                break;
            case 1:
            case 2:
                return $nodes->item(0)->getAttribute('href');
                break;
            default:
                //There are more than 2 bread crumbs
                if ($nodes->item(0)->getAttribute('href') == 'http://www.unl.edu/') {
                    //Most of the time, www.unl.edu will be the root, but we actually want the second.
                    return $nodes->item(1)->getAttribute('href');
                }
                return $nodes->item(0)->getAttribute('href');
        }

        return false;
    }

    /**
     * Get a array of PDF links
     * 
     * @param \DomXpath $xpath
     * @return array - an array of links.  Each link is an associative array with 'href' and 'html' values
     */
    public function getPDFLinks(\DomXpath $xpath)
    {
        $links = array();
        $nodes = $xpath->query("//xhtml:a");

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');
            
            if (strtolower(substr($href, -4)) == '.pdf') {
                $links[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }
        
        return $links;
    }

    /**
     * Get a list of flash objects
     * 
     * @param \DomXpath $xpath
     * @return array - an array of objects.  Each link is an associative array with 'file' and 'html' values
     */
    public function getFlashObjects(\DomXpath $xpath)
    {
        $objects = array();
        $nodes = $xpath->query("//xhtml:object");

        foreach ($nodes as $node) {
            $file = $node->getAttribute('data');

            if (strtolower(substr($file, -4)) == '.swf') {
                $objects[] = array(
                    'value_found' => $file,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $objects;
    }

    /**
     * Get a list of flash objects
     *
     * @param \DomXpath $xpath
     * @return array - an array of objects.  Each link is an associative array with 'file' and 'html' values
     */
    public function getIconFontErrors(\DomXpath $xpath)
    {
        $errors = array(
            self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => array(),
            self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => array(),
        );
        
        $nodes = $xpath->query("//xhtml:*[@id='maincontent']//xhtml:*[contains(@class,'wdn-icon-')]");

        foreach ($nodes as $node) {
            //perform tests
            $node_value = preg_replace('/\s+/', '', $node->nodeValue);
            $context = htmlspecialchars($xpath->document->saveHTML($node));
            
            //compute the value_found (icon class)
            $icon_class = '';
            $classes = explode(' ', $node->getAttribute('class'));
            foreach ($classes as $class) {
                if (0 === strpos($class, 'wdn-icon-')) {
                    $icon_class = $class;
                }
            }
            
            if (!empty($node_value)) {
                $errors[self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS][] = array(
                    'value_found' => $icon_class,
                    'context' => $context,
                );
            }
            
            
            if (!$node->hasAttribute('aria-hidden') || 'true' != $node->getAttribute('aria-hidden')) {
                $errors[self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN][] = array(
                    'value_found' => $icon_class,
                    'context' => $context,
                );
            }
        }

        return $errors;
    }

    /**
     * Get a list of instances of "UNL" and "University of Nebraska--Lincoln" in the text of the document
     *
     * @param \DomXpath $xpath
     * @return array - an array of the textual references to "UNL" or similar. Each is an associative array with 'context', 'value_found' values
     */ 
    public function getBrandInconsistencyReferences(\DomXpath $xpath)
    {
        $errors = array();

        $nodes = $xpath->query("//*[@id='wdn_content_wrapper']//text()");
        foreach ($nodes as $node) {
            $unls = substr_count($node->textContent, 'UNL');
            if ($unls > 0) {
                $errors[] = array(
                    'value_found' => 'UNL',
                    'count' => $unls,
                    'context' => $node->textContent
                );
            }
            $full_names = 
                substr_count($node->textContent, 'University of Nebraska-Lincoln') + //normal dash
                substr_count($node->textContent, 'University of Nebraska--Lincoln') +  //two dashes
                substr_count($node->textContent, 'University of NebraskaLincoln') + //no space
                substr_count($node->textContent, 'University of Nebraska–Lincoln') + //emdash
                substr_count($node->textContent, 'University of Nebraska—Lincoln') + //endash
                substr_count($node->textContent, 'University of Nebraska Lincoln'); //space
            if ($full_names > 1) {
                $errors[] = array(
                    'value_found' => 'University of Nebraska-Lincoln',
                    'count' => $full_names,
                    'context' => $node->textContent
                );
            }
        }

        return $errors;
    }
}
