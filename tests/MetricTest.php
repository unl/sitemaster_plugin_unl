<?php
namespace SiteMaster\Plugins\Unl;

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


    public function getTestXPath($filename)
    {
        $parser = new \Spider_Parser();
        $html = file_get_contents(__DIR__ . '/data/' . $filename);
        return $parser->parse($html);
    }
}