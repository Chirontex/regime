<?php
/**
 * @package Regime
 */
namespace Regime\Models;

/**
 * Class for redirects.
 * @since 0.9.1
 */
class Relocator
{

    /**
     * @var string $uri
     * Redirect URI.
     * @since 0.9.1
     */
    protected $uri;

    /**
     * Class constructor.
     * @since 0.9.1
     * 
     * @param string $uri
     * Redirect URI.
     */
    public function __construct(string $uri)
    {
        
        if (empty($uri)) $uri = '/';

        $this->uri = $uri;

        add_action('wp_headers', function($headers) {

            $headers = [];
            $headers['Location'] = site_url($this->uri);

            return $headers;

        });

        add_action('wp', function() {

            status_header(302);

        });

    }

}
