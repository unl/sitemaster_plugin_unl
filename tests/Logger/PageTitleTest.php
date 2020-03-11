<?php
namespace SiteMaster\Plugins\Unl\Logger;

use SiteMaster\Core\Auditor\Parser\HTML5;
use SiteMaster\Core\Auditor\Site\Page;
use SiteMaster\Plugins\Unl\Plugin;

class PageTitleTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function getPageTitle()
    {
        $plugin = new Plugin();
        $logger = new PageTitle(new Page());
        $parser = new HTML5();
        $html = file_get_contents($plugin->getRootDirectory() . '/tests/data/template_5_1.html');
        $xpath = $parser->parse($html);

        $this->assertEquals('Content Resource Examples', $logger->getPageTitle($xpath));
    }
}