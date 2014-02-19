<?php
namespace SiteMaster\Plugins\Unl;

class TemplateVersionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function get()
    {
        $version_helper = new TemplateVersions();
        
        //We should check againt live data, not cached data
        $versions = $version_helper->get(false);

        /**
         * We can't predict what the versions will be,
         * So just make sure that the version arrays are not empty
         */
        $this->assertEquals(false, empty($versions['html']));
        $this->assertEquals(false, empty($versions['dep']));
    }

    /**
     * @test
     */
    public function isCurrent()
    {
        $version_helper = new TemplateVersions();
        $version_helper->versions = array(
            'html' => array('4.0'),
            'dep' => array('4.0.1')
        );

        $this->assertEquals(true, $version_helper->isCurrent('4.0', TemplateVersions::VERSION_NAME_HTML));
        $this->assertEquals(false, $version_helper->isCurrent('3.0', TemplateVersions::VERSION_NAME_HTML));
        $this->assertEquals(true, $version_helper->isCurrent('4.0.1', TemplateVersions::VERSION_NAME_DEP));
        $this->assertEquals(false, $version_helper->isCurrent('3.0', TemplateVersions::VERSION_NAME_DEP));
    }
}