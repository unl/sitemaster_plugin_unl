<?php
namespace SiteMaster\Plugins\Unl\Logger;

use DOMXPath;

use SiteMaster\Core\Auditor\Logger\PageTitleInterface;
use SiteMaster\Core\Auditor\Site\Page;

class PageTitle extends PageTitleInterface
{
    /**
     * Get the Page Title
     *
     * @param DOMXPath $xpath the xpath of the page
     * @return bool|string the page title
     */
    public function getPageTitle(DOMXPath $xpath)
    {
        if ($title = $this->getH1($xpath)) {
            //use the H1 if available
            return $title;
        }
        
        return $this->getTitle($xpath);
    }
    
    public function getH1(DOMXPath $xpath)
    {
        if (!$result = $xpath->query("//xhtml:h1")) {
            return false;
        }

        if (!$result->length) {
            return false;
        }

        return trim(strip_tags($result->item(0)->textContent));
    }
    
    public function getTitle(DOMXPath $xpath)
    {
        if (!$result = $xpath->query('//xhtml:title')) {
            return false;
        }

        if (!$result->length) {
            return false;
        }

        return $result->item(0)->textContent;
    }
}
