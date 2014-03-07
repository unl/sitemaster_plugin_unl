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
        if (!$result = $xpath->query("//xhtml:div[@id='pagetitle']")) {
            return false;
        }

        if (!$result->length) {
            return false;
        }

        return trim(strip_tags($result->item(0)->textContent));
    }
}
