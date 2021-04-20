<?php
/**
 * @package Regime
 */
namespace Regime\Interfaces;

/**
 * Admin page container interface.
 */
interface IAdminMenuPage
{

    /**
     * Get view.
     * @since 0.3.5
     * 
     * @return string
     */
    public function viewGet() : string;

    /**
     * Get menu title.
     * @since 0.3.5
     * 
     * @return string
     */
    public function menuTitleGet() : string;

    /**
     * Get page title.
     * @since 0.3.5
     * 
     * @return string
     */
    public function pageTitleGet() : string;

    /**
     * Get slug.
     * @since 0.3.5
     * 
     * @return string
     */
    public function slugGet() : string;

}
