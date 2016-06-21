<?php
namespace SiteMaster\Plugins\Unl;

use SiteMaster\Core\Auditor\Parser\HTML5;
use \SiteMaster\Plugins\Unl\Metric as UNLMetric;

class MetricTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function getHTMLVersion()
    {
        $metric = new Metric('unl');
        
        $xpath_template_4_0     = $this->getTestXPath('template_4_0.html');
        $xpath_template_3_1     = $this->getTestXPath('template_3_1.html');
        $xpath_template_3_0     = $this->getTestXPath('template_3_0.html');
        $xpath_template_unknown = $this->getTestXPath('example.html');
        
        $this->assertEquals('4.0', $metric->getHTMLVersion($xpath_template_4_0));
        $this->assertEquals('3.1', $metric->getHTMLVersion($xpath_template_3_1));
        $this->assertEquals('3.0', $metric->getHTMLVersion($xpath_template_3_0));
        $this->assertEquals(null, $metric->getHTMLVersion($xpath_template_unknown));
    }

    /**
     * @test
     */
    public function getDEPVersion()
    {
        $metric = new Metric('unl');

        $xpath_template_4_0     = $this->getTestXPath('template_4_0.html');
        $xpath_template_3_1     = $this->getTestXPath('template_3_1.html');
        $xpath_template_3_0     = $this->getTestXPath('template_3_0.html');
        $xpath_template_unknown = $this->getTestXPath('example.html');

        $this->assertEquals('4.0.9', $metric->getDEPVersion($xpath_template_4_0));
        $this->assertEquals('3.1.19', $metric->getDEPVersion($xpath_template_3_1));
        $this->assertEquals('3.0', $metric->getDEPVersion($xpath_template_3_0));
        $this->assertEquals(null, $metric->getDEPVersion($xpath_template_unknown));
    }

    /**
     * @test
     */
    public function getYouTubeEmbeds()
    {
        $metric = new Metric('unl');
        
        $xpath_template = $this->getTestXPath('example.html');
        $this->assertEquals(array('//www.youtube.com/embed/SxPE9xwsXTs'), $metric->getYouTubeEmbeds($xpath_template));

        $xpath_template = $this->getTestXPath('template_4_0.html');
        $this->assertEquals(array(), $metric->getYouTubeEmbeds($xpath_template));
    }

    /**
     * @test
     */
    public function getRootSiteURL()
    {
        $metric = new Metric('unl');

        $xpath_template = $this->getTestXPath('template_3_0.html');
        $this->assertEquals('http://www.unl.edu/', $metric->getRootSiteURL($xpath_template));
        $xpath_template = $this->getTestXPath('template_3_1.html');
        $this->assertEquals('http://www.unl.edu/', $metric->getRootSiteURL($xpath_template));
        $xpath_template = $this->getTestXPath('template_4_0.html');
        $this->assertEquals('http://wdn.unl.edu/', $metric->getRootSiteURL($xpath_template));
    }

    /**
     * @test
     */
    public function getPDFLinks()
    {
        $metric = new Metric('unl');

        $xpath_template = $this->getTestXPath('example.html');
        $links = $metric->getPDFLinks($xpath_template);
        $this->assertEquals('test.pdf', $links[0]['value_found']);
    }

    /**
     * @test
     */
    public function getFlashObjects()
    {
        $metric = new Metric('unl');

        $xpath_template = $this->getTestXPath('example.html');
        $links = $metric->getFlashObjects($xpath_template);
        $this->assertEquals('your-flash-file.swf', $links[0]['value_found']);
    }

    /**
     * @test
     */
    public function getIconFontErrors()
    {
        $metric = new Metric('unl');

        $xpath_template = $this->getTestXPath('icon-font.html');
        $errors = $metric->getIconFontErrors($xpath_template);
        
        //Should only have found 1 element for both errors
        $this->assertEquals(1, count($errors[UNLMetric::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN]));
        $this->assertEquals(1, count($errors[UNLMetric::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS]));
        
        //Make sure we found the right elements
        $this->assertEquals('wdn-icon-no-aria-hidden', $errors[UNLMetric::MARK_MN_UNL_FRAMEWORK_ICON_FONT_NOT_ARIA_HIDDEN][0]['value_found']);
        $this->assertEquals('wdn-icon-has-contents', $errors[UNLMetric::MARK_MN_UNL_FRAMEWORK_ICON_FONT_HAS_CONTENTS][0]['value_found']);
    }

    public function getTestXPath($filename)
    {
        $parser = new HTMl5();
        $html = file_get_contents(__DIR__ . '/data/' . $filename);
        return $parser->parse($html);
    }
}
