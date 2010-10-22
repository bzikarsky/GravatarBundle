<?php

namespace Bundle\GravatarBunde\Tests;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Bundle\GravatarBundle\GravatarApi;

class GravatarApiTest extends TestCase
{
    public function testGravatarThumbnailUrlWithDefaultOptions()
    {
        $api = new GravatarApi();
        $this->assertEquals('http://www.gravatar.com/avatar/0aa61df8e35327ac3b3bc666525e0bee?s=80&r=g', $api->getThumbnailUrl('henrik@bearwoods.dk'));
    }

    public function testGravatarThumbnailUrlWithDefaultImage()
    {
        $api = new GravatarApi();
        $this->assertEquals('http://www.gravatar.com/avatar/0aa61df8e35327ac3b3bc666525e0bee?s=80&r=g&d=mm', $api->getThumbnailUrl('henrik@bearwoods.dk', 80, 'g', 'mm'));
    }
    
    public function testGravatarProfileUrlWithDefaultOptions()
    {
		$api = new GravatarApi();
		$this->assertEquals('http://www.gravatar.com/0aa61df8e35327ac3b3bc666525e0bee', $api->getProfileUrl('henrik@bearwoods.dk'));
    }
    
    public function testGravatarProfileUrlWithOptions()
    {
		$api = new GravatarApi();
		$this->assertEquals('http://www.gravatar.com/0aa61df8e35327ac3b3bc666525e0bee.qr?s=200', $api->getProfileUrl('henrik@bearwoods.dk', 'qr', array('s' => 200)));
    }
    
    public function testGetExistingGravatarProfile()
    {
		$api = new GravatarApi();
		$profile = $api->getProfile('henrik@bearwoods.dk');
		$this->assertArrayHasKey('entry', $profile);
		$this->assertEquals(1, count($profile['entry']));
		
		$entry = $profile['entry'][0];
		$this->assertArrayHasKey('hash', $entry);
		$this->assertArrayHasKey('id', $entry);
		$this->assertEquals('0aa61df8e35327ac3b3bc666525e0bee', $entry['hash']);
		$this->assertEquals('3342623', $entry['id']);
    }
    
    public function testGetGravatarProfileCache()
    {
		$tmpDir = dirname(__FILE__) . '/tmp';
		mkdir($tmpDir);
		
		$api = new GravatarApi();
		$cache = \Zend\Cache\Cache::factory('Core', 'File', 
			array('cache_id_prefix' => 'gravatar'),
			array('cache_dir' => $tmpDir)
		);
		
		$api->setCache($cache);
		$api->getProfile('henrik@bearwoods.dk');
		
		// test() returns an integer if there is an entry, therefore cast to bool
		$this->assertTrue((bool) $cache->test(
			md5($api->getProfileUrl('henrik@bearwoods.dk', 'php'))
		));
		
		$this->assertFalse($cache->test(
			md5($api->getProfileUrl('somefake@example.com', 'php'))
		));
		
		$cache->clean();
		
		$this->assertFalse($cache->test(
			md5($api->getProfileUrl('henrik@bearwoods.dk', 'php'))
		));
		
		rmdir($tmpDir);
    }
    
    public function testGetNonExistingGravatarProfile()
    {
		$api = new GravatarApi();
		$this->assertEquals(null, $api->getProfile('somefake@example.com'));
    }

    public function testGravatarInitializedWithOptions()
    {
        $api = new GravatarApi(array(
            'size' => 20,
            'default' => 'mm',
            'format' => 'xml'
        ));

        $this->assertEquals('http://www.gravatar.com/avatar/0aa61df8e35327ac3b3bc666525e0bee?s=20&r=g&d=mm', $api->getThumbnailUrl('henrik@bearwoods.dk'));
        $this->assertEquals('http://www.gravatar.com/0aa61df8e35327ac3b3bc666525e0bee.xml', $api->getProfileUrl('henrik@bearwoods.dk'));
    }

    public function testGravatarExists()
    {
        $api = new GravatarApi();

        $this->assertFalse($api->exists('somefake@email.com'));
        $this->assertTrue($api->exists('henrik@bearwoods.dk'));
    }

}
