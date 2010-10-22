<?php

namespace Bundle\GravatarBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\HelperInterface;
use Bundle\GravatarBundle\GravatarApi;

/**
 * Symfony 2 Helper for Gravatar. Uses Bundle\GravatarBundle\GravatarApi
 *
 * @author Thibault Duplessis
 * @author Henrik Bjornskov <henrik@bearwoods.dk>
 * @author     Benjamin Zikarsky <benjamin@zikarsky.de>
 */
class GravatarHelper implements HelperInterface
{
    /**
     * @var string $charset
     */
    protected $charset = 'UTF-8';

    /**
     * @var Bundle\GravatarBundle\GravatarApi $api
     */
    protected $api;

    /**
     * Constructor
     *
     * @param Bundle\GravatarBundle\GravatarApi $api
     * @return void
     */
    public function __construct(GravatarApi $api)
    {
        $this->api = $api;
    }

    /**
     * Returns a url for a gravatar
     *
     * @param  string  $email
     * @param  integer $size
     * @param  string  $rating
     * @param  string  $default
     * @return string
     */
    public function getThumbnailUrl($email, $size = null, $rating = null, $default = null)
    {
        return $this->api->getThumbnailUrl($email, $size, $rating, $default);
    }
    
    /**
     * Returns a url for a gravatar. Deprecated in favour of getThumbnailUrl
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
        return $this->api->getThumbnailUrl($email, $size, $rating, $default);
    }

    /**
     * Returns true if a avatar could be found for the email
     *
     * @param string $email
     * @return boolean
     */
    public function exists($email)
    {
        return $this->api->exists($email);
    }
    
    /**
     * Returns an url for a gravatar profile
     *
     * @param  string $email
     * @param  string $format
     * @param  array  $params (optional)
     * @return string
     */
    public function getProfileUrl($email, $format = null, array $params = array())
    {
		return $this->api->getProfileUrl($email, $format, $params);
    }

    /**
     * Sets the default charset.
     *
     * @param string $charset The charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Name of this Helper
     *
     * @return string
     */
    public function getName()
    {
        return 'gravatar';
    }
}
