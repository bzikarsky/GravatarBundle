<?php

namespace Bundle\GravatarBundle\Tests\Templating\Helper;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Bundle\GravatarBundle\GravatarApi;
use Bundle\GravatarBundle\Templating\Helper\GravatarHelper;

class GravatarHelperTest extends TestCase
{
    protected $helper;

    public function setUp()
    {
        $this->helper = new GravatarHelper(new GravatarApi());
    }

    public function testGetThumbnailUrlReturnsTheCorrectUrl()
    {
        $this->assertEquals('http://www.gravatar.com/avatar/0aa61df8e35327ac3b3bc666525e0bee?s=80&r=g', $this->helper->getThumbnailUrl('henrik@bearwoods.dk'));
    }

    public function testCheckForAvatarExistance()
    {
        $this->assertTrue($this->helper->exists('henrik@bearwoods.dk'));
        $this->assertFalse($this->helper->exists('someone@someonefake.lol'));
    }
    
    public function testGetProfileUrlReturnsTheCorrectUrl()
    {
		$this->assertEquals('http://www.gravatar.com/0aa61df8e35327ac3b3bc666525e0bee', $this->helper->getProfileUrl('henrik@bearwoods.dk'));
    }
}
