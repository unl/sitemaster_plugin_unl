<?php
namespace SiteMaster\Plugins\Unl;

class FrameworkVersionHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @test
     */
    public function get()
    {
        $version_helper = new FrameworkVersionHelper(array('cache'=>false, 'autoload'=>false));
        
        //We should check against live data, not cached data
        $versions = $version_helper->grabVersions(false);

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
        $version_helper = new FrameworkVersionHelper(array('cache'=>false, 'autoload'=>false));
        $version_helper->setVersions(array(
            'html' => array('5.1'),
            'dep' => array('5.1.5')
        ));

        $this->assertEquals(true, $version_helper->isCurrent('5.1', FrameworkVersionHelper::VERSION_NAME_HTML));
        $this->assertEquals(false, $version_helper->isCurrent('5.0', FrameworkVersionHelper::VERSION_NAME_HTML));
        $this->assertEquals(false, $version_helper->isCurrent('4.0', FrameworkVersionHelper::VERSION_NAME_HTML));
        $this->assertEquals(false, $version_helper->isCurrent('3.0', FrameworkVersionHelper::VERSION_NAME_HTML));
        $this->assertEquals(true, $version_helper->isCurrent('5.1.5', FrameworkVersionHelper::VERSION_NAME_DEP));
        $this->assertEquals(false, $version_helper->isCurrent('4.0.1', FrameworkVersionHelper::VERSION_NAME_DEP));
        $this->assertEquals(false, $version_helper->isCurrent('3.0', FrameworkVersionHelper::VERSION_NAME_DEP));
    }
}
