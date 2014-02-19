<?php
namespace SiteMaster\Plugins\Unl;

class MetricTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function get()
    {
        $template_versions = new TemplateVersions();
        
        //We should check againt live data, not cached data
        $versions = $template_versions->get(false);

        /**
         * We can't predict what the versions will be,
         * So just make sure that the version arrays are not empty
         */
        $this->assertEquals(false, empty($versions['html']));
        $this->assertEquals(false, empty($versions['dep']));
    }
}