<?php
/**
 * @package Regime
 */
namespace Regime;

use wpdb;

/**
 * @abstract
 * App point of entry abstract class.
 * @since 0.0.2
 */
abstract class PointOfEntry
{

    /**
     * @var wpdb $wpdb
     * WordPress database object.
     * @since 0.0.2
     */
    protected $wpdb;

    /**
     * @var string $path
     * Plugin root directory.
     * @since 0.0.2
     */
    protected $path;

    /**
     * @var string $url
     * Plugin root directory as URL.
     * @since 0.0.2
     */
    protected $url;

    /**
     * @param string $path
     * Plugin root directory.
     * 
     * @param string $url
     * Plugin root directory as URL.
     * 
     * @since 0.0.2
     */
    public function __construct(string $path, string $url)
    {
        
        global $wpdb;

        $this->wpdb = $wpdb;

        $this->path = $path;

        $this->url = $url;

    }

}
