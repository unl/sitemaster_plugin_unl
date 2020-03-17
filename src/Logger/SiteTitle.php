<?php
namespace SiteMaster\Plugins\Unl\Logger;

use DOMXPath;

use SiteMaster\Core\Auditor\Site\Page;

class SiteTitle
{
    /**
     * Get the Site Title
     * 
     * This should get the site title for 4.0 and 3.1 sites.
     *
     * @param DOMXPath $xpath the xpath of the page
     * @return bool|string the site title
     */
    public function getSiteTitle(DOMXPath $xpath)
    {
        if (!$result = $xpath->query("//xhtml:*[@id='wdn_site_title']|//xhtml:*[@id='dcf-site-title']")) {
            return false;
        }

        if (!$result->length) {
            return false;
        }

        $title = strip_tags($result->item(0)->textContent);

        //replace any extra spaces and newlines with just one space
        return trim(preg_replace('/\s+/', ' ', $title));
    }
}
