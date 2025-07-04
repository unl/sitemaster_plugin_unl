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
    const MARK_MN_UNL_FRAMEWORK_BOX_LINK = 'UNL_FRAMEWORK_BOX';
    const MARK_MN_UNL_FRAMEWORK_VIDGRID = 'UNL_FRAMEWORK_VIDGRID';
    const MARK_MN_UNL_FRAMEWORK_POLYFILL = 'UNL_FRAMEWORK_POLYFILL';
    const MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN = 'UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN';
    const MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS = 'UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS';
    const MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES = 'UNL_FRAMEWORK_BRAND_INCONSISTENCIES';
    const MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLES_FILE = 'UNL_FRAMEWORK_WDN_DEPRECATED_STYLES_FILE';
    const MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLE_REFERENCES = 'UNL_FRAMEWORK_WDN_DEPRECATED_STYLE_REFERENCES';
    const MARK_MN_UNL_FRAMEWORK_INCORRECT_RSO_REFERENCES = 'UNL_FRAMEWORK_INCORRECT_RSO_REFERENCES';
    const MARK_MN_UNL_FRAMEWORK_ONE_DRIVE_SHAREPOINT_LINK = 'UNL_FRAMEWORK_ONE_DRIVE_SHAREPOINT_LINK';

    /**
     * @param string $plugin_name
     * @param array $options
     */
    public function __construct($plugin_name, array $options = array())
    {
        $optionsConfig = array(
            self::MARK_MN_UNL_FRAMEWORK_HTML => array(
                'title_text' => 'The UNLedu framework HTML is out of date',
                'description_text' => 'The UNLedu framework HTML is out of date',
                'help_text' => 'For mirroring instructions, see [Synchronizing the UNLedu Web Framework](https://github.com/unl/wdntemplates/wiki/Synchronizing-the-Framework)',
                'point_deductions' => 80
            ),
            self::MARK_MN_UNL_FRAMEWORK_DEP => array(
                'title_text' => 'The UNLedu framework dependents are out of date',
                'description_text' => 'The UNLedu framework dependents are out of date',
                'help_text' => 'For mirroring instructions, see [Synchronizing the UNLedu Web Framework](https://github.com/unl/wdntemplates/wiki/Synchronizing-the-Framework)',
                'point_deductions' => 20
            ),
            self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE => array(
                'title_text' => 'A Youtube Embed was found',
                'description_text' => 'It is important to keep in mind that youtube is blocked in some places around the world, including China.  It is a best practice to host video on mediahub.unl.edu, where the video will not be blocked.',
                'help_text' => 'Host the video from [Mediahub](http://mediahub.unl.edu/)',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS => array(
                'title_text' => 'A PDF was found. Please independently ensure PDF accessibility',
                'description_text' => 'Please ensure that the PDF is accessible.',
                'help_text' => 'See [webaim](http://webaim.org/techniques/acrobat/) for help with PDF accessibility.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT => array(
                'title_text' => 'A flash object was found',
                'description_text' => 'The use of flash is discouraged as it does not work on most mobile devices',
                'help_text' => 'Either remove the flash object, or replace it with an HTML5 alternative.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_BOX_LINK => array(
                'title_text' => 'A box.com link was found which may now be invalid due to UNL migrating from Box to Office 365 services for document storage.',
                'description_text' => 'Please ensure this link remains valid during this transition, and change it to point to its new location as soon as your documents have been moved.',
                'help_text' => 'Verify the box.com link is still valid, and if not remove or replace with current link location.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_VIDGRID => array(
                'title_text' => 'A element containing VidGrid was found.',
                'description_text' => 'Please ensure this element has been updated to Yuja, or has been removed.',
                'help_text' => 'Please update or remove references to VidGrid.',
                'point_deductions' => 1
            ),
            self::MARK_MN_UNL_FRAMEWORK_POLYFILL => array(
                'title_text' => 'Polyfill.io link has been found.',
                'description_text' => 'Polyfill.io has changed ownership and is now considered malware.',
                'help_text' => 'Please update or remove references to Polyfill.io.',
                'point_deductions' => 1
            ),
            self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN => array(
                'title_text' => 'An icon font was found without aria-hidden="true"',
                'description_text' => 'Screen readers might read icon-fonts and convey an incorrect or confusing meaning. Icon fonts should be hidden from screen readers.',
                'help_text' => 'See [the WDN icon-font documentation](https://github.com/unl/wdntemplates/wiki/Icons) for help with accessibility.',
                'point_deductions' => 1
            ),
            self::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS => array(
                'title_text' => 'An icon font is applied to an element with contents',
                'description_text' => 'Because icon fonts should be hidden from screen readers with aria-hidden="true", the element containing the icon font and text within it will not be read.',
                'help_text' => 'See [the WDN icon-font documentation](https://github.com/unl/wdntemplates/wiki/Icons) for help with accessibility.',
                'point_deductions' => 1
            ),
            self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES => array(
                'title_text' => 'Style inconsistencies were found with the University of Nebraska style guide.',
                'description_text' => 'In written communication, the full name, University of Nebraska–Lincoln, should be spelled out when the university is first mentioned or cited. Thereafter, references should cite “the university” or “Nebraska.”',
                'help_text' => 'See [the brand book](http://unlcms.unl.edu/ucomm/styleguide/u#UNL-abbrev) for more information on this topic.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLES_FILE => array(
                'title_text' => 'Found use of deprecated WDN CSS file.',
                'description_text' => 'WDN styles (e.g., classes such as "wdn-band") should no longer be used. Support for WDN styles will begin to be phased out in July 2022. Please use the WDN styles documentation link below for the phase out schedule.',
                'help_text' => 'See [the WDN styles documentation](https://wdn.unl.edu/documentation/5.0/css/deprecated) for more information on this topic.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLE_REFERENCES => array(
                'title_text' => 'Found use of deprecated WDN style reference.',
                'description_text' => 'WDN styles (e.g., classes such as "wdn-band") should no longer be used. Support for WDN styles will begin to be phased out in July 2022. Please use the WDN styles documentation link below for the phase out schedule.',
                'help_text' => 'See [the WDN styles documentation](https://wdn.unl.edu/documentation/5.0/css/deprecated) for more information on this topic.',
                'point_deductions' => 0
            ),
            self::MARK_MN_UNL_FRAMEWORK_INCORRECT_RSO_REFERENCES => array(
                'title_text' => 'Found use of term "Registered Student Organization".',
                'description_text' => 'The term "Registered Student Organization" is incorrect, the correct term is "Recognized Student Organization".',
                'help_text' => 'Change text from "Registered Student Organization" to "Recognized Student Organization".',
                'point_deductions' => 1
            ),
            self::MARK_MN_UNL_FRAMEWORK_ONE_DRIVE_SHAREPOINT_LINK => array(
                'title_text' => 'Found OneDrive or Sharepoint link (They will need to be updated as part of the Office 365 transition).',
                'description_text' => 'Our scans indicate you have file links to Sharepoint or OneDrive on this website. If you intend to retain these as SharePoint or OneDrive links, they will need to be updated at the point of your unit\'s Office 365 transition.',
                'help_text' => 'A better practice for *necessary* file links would be to import them to your website.',
                'point_deductions' => 0
            ),
            'default'=> array(
                'title_text' => 'Framework Error',
                'description_text' => 'General UNLedu framework error',
                'help_text' => 'Fix this problem',
                'point_deductions' => 0
            )
        );
        $options = array_replace_recursive($optionsConfig, $options);

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
        $template_type = $this->getTemplateType($xpath);

        //Save these attributes for the page.
        PageAttributes::createPageAttributes($page->id, $html_version, $dep_version, $template_type);

        if (!$scan_attributes = ScanAttributes::getByScansID($scan->id)) {
            $scan_attributes = ScanAttributes::createScanAttributes($scan->id, $html_version, $dep_version, $template_type);
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

            if (!is_null($template_type)) {
                $scan_attributes->template_type = $template_type;
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
            $items = array(array('value_found' => $html_version));
            $this->markMetric($page, $items, self::MARK_MN_UNL_FRAMEWORK_HTML, false);
        }

        if (!$version_helper->isCurrent($dep_version, FrameworkVersionHelper::VERSION_NAME_DEP)) {
            $items = array(array('value_found' => $dep_version));
            $this->markMetric($page, $items, self::MARK_MN_UNL_FRAMEWORK_DEP, false);
        }

        $this->markMetric($page,
            $this->getYouTubeEmbeds($xpath),
            self::MARK_MN_UNL_FRAMEWORK_YOUTUBUE,
            false);

        $this->markMetric($page,
            $this->getPDFLinks($xpath),
            self::MARK_MN_UNL_FRAMEWORK_PDF_LINKS,
            true);

        $this->markMetric($page,
            $this->getFlashObjects($xpath),
            self::MARK_MN_UNL_FRAMEWORK_FLASH_OBJECT,
            true);

        $this->markMetric($page,
            $this->getBoxLinks($xpath),
            self::MARK_MN_UNL_FRAMEWORK_BOX_LINK,
            true);

        $this->markMetric($page,
            $this->getVidGridReferences($xpath),
            self::MARK_MN_UNL_FRAMEWORK_VIDGRID,
            true);

        $this->markMetric($page,
            $this->getPolyfillReference($xpath),
            self::MARK_MN_UNL_FRAMEWORK_POLYFILL,
            true);

        $this->markMetric($page,
            $this->getIncorrectRSOReferences($xpath),
            self::MARK_MN_UNL_FRAMEWORK_INCORRECT_RSO_REFERENCES,
            true);

        $this->markMetric($page,
            $this->getOneDriveSharepointLinks($xpath),
            self::MARK_MN_UNL_FRAMEWORK_ONE_DRIVE_SHAREPOINT_LINK,
            true);

        $this->markMetric($page,
            $this->getBrandInconsistencyReferences($xpath),
            self::MARK_MN_UNL_FRAMEWORK_BRAND_INCONSISTENCIES,
            true);

        $this->markMetric($page,
            $this->getDeprecatedStyleFileReferences($xpath),
            self::MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLES_FILE,
            true);

        $this->markMetric($page,
            $this->getDeprecatedStyleReferences($xpath),
            self::MARK_MN_UNL_FRAMEWORK_WDN_DEPRECATED_STYLE_REFERENCES,
            true);

        $errors = $this->getIconFontErrors($xpath);
        foreach ($errors as $machineName => $elements) {
            $this->markMetric($page, $elements, $machineName, true);
        }
    }

    function markMetric(&$page, $items, $machineName, $withContext = false){
        if (!empty($items)) {
            $mark = $this->getMark(
                $machineName,
                $this->getMarkValue($machineName, 'title_text'),
                $this->getMarkValue($machineName, 'point_deductions'),
                $this->getMarkValue($machineName, 'description_text'),
                $this->getMarkValue($machineName, 'help_text')
            );
            foreach ($items as $item) {
                $markValues = array('value_found' => $item['value_found']);
                if ($withContext === true) {
                    $markValues['context'] = $item['context'];
                }
                $page->addMark($mark, $markValues);
            }
        }
    }

    public function getMarkValue($machineName, $name) {
        if (isset($this->options[$machineName][$name])) {
            return $this->options[$machineName][$name];
        } elseif (isset($this->options['default'][$name])) {
            return $this->options['default'][$name];
        }

        return $name . ' not defined' . ' for ' . $machineName;
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
     * Get the type of template being used from the comment in the head
     *
     * @param \DOMXPath $xpath the xpath of the page
     * @return null|string the version (null if not found)
     */
    public function getTemplateType(\DOMXPath $xpath)
    {
        $comments = array();

        // gets all the comments in the head tags
        $comment_nodes = $xpath->query("/xhtml:html/xhtml:head/comment()");
        foreach ($comment_nodes as $node) {
            $comments[] = $node->nodeValue;
        }

        // no comments were found
        if (empty($comments)) {
            return null;
        }

        // searches the comments for the filename with type .dwt
        $types_found = array();
        foreach ($comments as $comment) {
            $matches = array();
            if (preg_match('/[a-zA-Z_]*\.dwt/', $comment, $matches) && !empty($matches)) {
                foreach ($matches as $match) {
                    $types_found[] = $match;
                }
            }
        }

        // nothing was found in the comments
        if (empty($types_found)) {
            return null;
        }

        // removes the .dwt from the string
        return str_replace(".dwt", "", $types_found[0]);
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
            $sources[] = array(
                'value_found' => $node->getAttribute('src')
            );
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
            "(//xhtml:*[@id='breadcrumbs']/xhtml:ul/xhtml:li|//xhtml:*[@id='breadcrumbs']/xhtml:ul/xhtml:li/xhtml:span|//xhtml:*[@id='dcf-breadcrumbs']/xhtml:ol/xhtml:li|//xhtml:*[@id='dcf=breadcrumbs']/xhtml:ol/xhtml:li/xhtml:span)/xhtml:a"
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
     * Get a array of Box.com links
     *
     * @param \DomXpath $xpath
     * @return array - an array of links.  Each link is an associative array with 'href' and 'html' values
     */
    public function getBoxLinks(\DomXpath $xpath)
    {
        $links = array();
        $nodes = $xpath->query("//xhtml:a");

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');

            if (strpos($href, '.box.com') !== false) {
                $links[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $links;
    }

    /**
     * Get a array of vidgrid links
     *
     * @param \DomXpath $xpath
     * @return array - an array of links.  Each link is an associative array with 'href' and 'html' values
     */
    public function getVidGridReferences(\DomXpath $xpath)
    {
        $referencesToVidGrid = array();
        $nodes = $xpath->query("//xhtml:*[contains(text(),'vidgrid')]|//xhtml:*[contains(@href,'vidgrid')]|//xhtml:*[contains(@src,'vidgrid')]");

        foreach ($nodes as $node) {
            $text = $node->textContent;
            if (strpos($text, 'vidgrid')) {
                $referencesToVidGrid[] = array(
                    'value_found' => $text,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }

            $href = $node->getAttribute('href');
            if (strpos($href, 'vidgrid')) {
                $referencesToVidGrid[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }

            $src = $node->getAttribute('src');
            if (strpos($src, 'vidgrid')) {
                $referencesToVidGrid[] = array(
                    'value_found' => $src,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $referencesToVidGrid;
    }

    /**
     * Get a array of uses of incorrect term "Registered Student Organization"
     *
     * @param \DomXpath $xpath
     * @return array - An array of text nodes
     */
    public function getIncorrectRSOReferences(\DomXpath $xpath)
    {
        $errors = array();
        $nodes = $xpath->query("//*[@id='wdn_content_wrapper']//text()|//*[@id='dcf-main']//text()");

        foreach ($nodes as $node) {
            $incorrect_RSOs = substr_count($node->textContent, 'Registered Student Organization') +
            substr_count($node->textContent, 'registered student organization') +
            substr_count($node->textContent, 'Registered student organization');
            if ($incorrect_RSOs > 0) {
                $errors[] = array(
                    'value_found' => 'Registered Student Organization',
                    'count' => $incorrect_RSOs,
                    'context' => $node->textContent
                );
            }
        }

        return $errors;
    }

    /**
     * Get a array of uses of OneDrive or Sharepoint Links
     *
     * @param \DomXpath $xpath
     * @return array - An array of text nodes
     */
    public function getOneDriveSharepointLinks(\DomXpath $xpath) {
        $links = array();
        $nodes = $xpath->query("//xhtml:a");

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');

            if (
                strpos($href, 'my.sharepoint.com/:w:/r/') !== false ||
                strpos($href, 'sharepoint.com/:w:/r') !== false
            ) {
                $links[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $links;
    }

    /**
     * Get a array of polyfill.io links
     *
     * @param \DomXpath $xpath
     * @return array - an array of links.  Each link is an associative array with 'href' and 'html' values
     */
    public function getPolyfillReference(\DomXpath $xpath)
    {
        $referencesToPolyfill = array();
        $nodes = $xpath->query("//xhtml:*[contains(text(),'polyfill.io')]|//xhtml:*[contains(@href,'polyfill.io')]|//xhtml:*[contains(@src,'polyfill.io')]");

        foreach ($nodes as $node) {
            $text = $node->textContent;
            if (strpos($text, 'polyfill.io')) {
                $referencesToPolyfill[] = array(
                    'value_found' => $text,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }

            $href = $node->getAttribute('href');
            if (strpos($href, 'polyfill.io')) {
                $referencesToPolyfill[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }

            $src = $node->getAttribute('src');
            if (strpos($src, 'polyfill.io')) {
                $referencesToPolyfill[] = array(
                    'value_found' => $src,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $referencesToPolyfill;
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

        $nodes = $xpath->query("//xhtml:*[@id='maincontent']//xhtml:*[contains(@class,'wdn-icon-')]|//xhtml:*[@id='dcf-main']//xhtml:*[contains(@class,'wdn-icon-')]|//xhtml:*[@id='dcf-wdn_local_footer']//xhtml:*[contains(@class,'wdn-icon-')]|//xhtml:*[@id='dcf-footer']//xhtml:*[contains(@class,'wdn-icon-')]");
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

        $nodes = $xpath->query("//*[@id='wdn_content_wrapper']//text()|//*[@id='dcf-main']//text()");
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

    /**
     * Check for deprecated styles file references
     *
     * @param \DomXpath $xpath
     * @returnrray - an array of the textual references to deprecated file. Each is an associative array with 'context', 'value_found' values
     */
    public function getDeprecatedStyleFileReferences(\DomXpath $xpath)
    {
        $links = array();
        $nodes = $xpath->query("//xhtml:link");

        foreach ($nodes as $node) {
            $href = $node->getAttribute('href');

            if (preg_match('/wdn\/templates_5.\d+\/css\/deprecated.css/i', $href)) {
                $links[] = array(
                    'value_found' => $href,
                    'context' => htmlspecialchars($xpath->document->saveHTML($node))
                );
            }
        }

        return $links;
    }

    /**
     * Check for deprecated styles references
     *
     * @param \DomXpath $xpath
     * @returnrray - an array of the textual references to deprecated styles. Each is an associative array with 'context', 'value_found' values
     */
    public function getDeprecatedStyleReferences(\DomXpath $xpath)
    {
        $links = array();
        $nodes = $xpath->query("//xhtml:*[@id='dcf-main']//xhtml:*[contains(@class,'wdn-')]|//xhtml:*[@id='dcf-main']//xhtml:*[contains(@class,'wdn_')]|//xhtml:*[@id='dcf-footer']//xhtml:*[contains(@class,'wdn-')]|//xhtml:*[@id='dcf-footer']//xhtml:*[contains(@class,'wdn_')]");

        foreach ($nodes as $node) {
            $classMatch = $node->getAttribute('class');
            $classes = preg_split("/\s+/", $classMatch);
            foreach ($classes as $class) {
                // only flag classes which start with wdn or contains '-wdn-col-'
                if (substr(strtolower($class), 0, 3) === 'wdn' || strpos(strtolower($class), '-wdn-col-')) {
                    $links[] = array(
                        'value_found' => $class,
                        'context' => htmlspecialchars($xpath->document->saveHTML($node))
                    );
                }
            }
        }

        return $links;
    }
}
