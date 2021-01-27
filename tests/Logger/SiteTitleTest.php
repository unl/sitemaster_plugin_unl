<?php
namespace SiteMaster\Plugins\Unl\Logger;

use SiteMaster\Core\Auditor\Parser\HTML5;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Plugins\Unl\Plugin;

class SiteTitleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getPageTitle()
    {
        $plugin = new Plugin();
        $logger = new SiteTitle(new Page());
        $parser = new HTML5();

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_5_3.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_5_2.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_5_1.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_5_0.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_4_0.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_3_1.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Web Developer Network', $logger->getSiteTitle($xpath));

        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_3_0.html');
        $xpath = $parser->parse($html);

        //Don't bother getting the site title for 3.0 sites
        $this->assertEquals(false, $logger->getSiteTitle($xpath));
    }
}
