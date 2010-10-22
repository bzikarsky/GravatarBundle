<?php

namespace Bundle\GravatarBundle;

/**
 * Simple wrapper to the gravatar API
 * http://en.gravatar.com/site/implement/url
 *
 * Usage:
 *      \Bundle\GravatarBundle\GravatarApi::getThumbnailUrl('henrik@bearwoods.dk', 80, 'g', 'mm');
 *
 * @author     Thibault Duplessis <thibault.duplessis@gmail.com>
 * @author     Henrik Bjørnskov <henrik@bearwoods.dk>
 * @author     Benjamin Zikarsky <benjamin@zikarsky.de>
 */
class GravatarApi
{
    /**
     * @var array $default array of default options that can be overriden with getters and in the construct.
     */
    protected $defaults = array(
        'size'           => 80,
        'rating'         => 'g',
        'default'        => null,
        'format'         => ''
    );
    
    /**
     * @var \Zend\Http\Client $httpClient 
     */
    protected $httpClient = null;
    
    /**
     * @var \Zend\Cache\Frontend $cache
     */
    protected $cache = null;

    /**
     * Constructor
     *
     * @param array $options the array is merged with the defaults.
     * @return void
     */
    public function __construct(array $options = array())
    {
        $this->defaults = array_merge($this->defaults, $options);
    }

    /**
     * Returns a thumbnail url for a gravatar.
     *
     * @param  string  $email
     * @param  integer $size
     * @param  string  $rating
     * @param  string  $default
     * @return string
     */
    public function getThumbnailUrl($email, $size = null, $rating = null, $default = null)
    {
        $hash = $this->hash($email);
        $map = array(
            's' => $size    ?: $this->defaults['size'],
            'r' => $rating  ?: $this->defaults['rating'],
            'd' => $default ?: $this->defaults['default'],
        );

        return 'http://www.gravatar.com/avatar/' . $hash . '?' . http_build_query(array_filter($map));
    }
    
    /**
     * Returns a thumbnail url for a gravatar. Deprecated in favour of getThumbnailUrl
     *
     * @deprecated
     * @param  string  $email
     * @param  integer $size
     * @param  string  $rating
     * @param  string  $default
     * @return string
     */
    public function getUrl($email, $size = null, $rating = null, $default = null)
    {
		return $this->getThumbnailUrl($email, $size, $rating, $default);
    }

	/**
	 * Get an url to a grvatar profile
	 *
	 * @param  string $email
	 * @param  string $format
	 * @param  array  $params (optional)
	 * @return string
	 */
	public function getProfileUrl($email, $format = null, array $params = array())
	{
		$url = 'http://www.gravatar.com/' . $this->hash($email);
		
		// add an optional format
		$format = $format ?: $this->defaults['format'];
		if (strlen($format)) {
			$url .= '.' . $format;
		}
		
		// add optional paramaters
		$query = http_build_query(array_filter($params));
		if ($query) {
			$url .= '?' . $query;
		}
		
		return $url;
	}
	
	/**
	 * Get a Gravatar profile or null if the email does not have a profile
	 *
	 * @param  string     $email
	 * @return null|array
	 */
	public function getProfile($email)
	{
		$url = $this->getProfileUrl($email, 'php');
		$cacheId = md5($url);
		$cache = $this->getCache();
		
		// check cache
		if ($cache->test($cacheId)) {
			return unserialize($cache->load($cacheId));
		}
		
		// request profile
		$response = $this->getHttpClient()
			->setUri($url)
			->request('GET');
			
		// check profile existence
		if ($response->getStatus() != 200) {
			return null;
		}
		
		// cache relies on "Expires" header
		if ($response->getHeader('Expires')) {
			$serverTime = new \DateTime($response->getHeader('Date'));
			$serverExpires = new \DateTime($response->getHeader('Expires'));
			$expiresIn = $serverTime->diff($serverExpires)->s;
			$cache->save($response->getBody(), $cacheId, array(), $expiresIn);		
		}
		
		return unserialize($response->getBody());
	}
	
	/**
	 * Returns the hash to a given email-address
	 *
	 * @param  string email
	 * @return string
	 */
	public function hash($email)
	{
		return md5(strtolower(trim($email)));
	}
	
    /**
     * Checks if a gravatar exists for the email. 
     *
     * @param string $email
     * @return boolean
     */
    public function exists($email)
    {
        return 200 == $this->getHttpClient()
            ->setUri($this->getProfileUrl($email, 'json'))
            ->request('HEAD')
            ->getStatus();
    }
    
    /**
     * Set the cache
     *
     * @param \Zend\Cache\Frontend $cache
     */
    public function setCache(\Zend\Cache\Frontend $cache)
    {
		$this->cache = $cache;
    }
    
    /**
     * Returns the cache
     *
     * @return \Zend\Cache\Frontend
     */
    public function getCache()
    {
		if (!$this->cache) {
			$this->setCache(\Zend\Cache\Cache::factory('Core', 'BlackHole'));
		}
		
		return $this->cache;
    }
    
    /**
     * Set the HttpClient
     *
     * @param \Zend\Http\Client
     */
    public function setHttpClient(\Zend\Http\Client $client)
    {
		$this->httpClient = $client;
    }
    
    /**
     * Get the HttpClient
     *
     * @return \Zend\Http\Client
     */
    public function getHttpClient()
    {
		if (!$this->httpClient) {
			$this->setHttpClient(new \Zend\Http\Client());
		}
		
		return $this->httpClient;
    }
}
