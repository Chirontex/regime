<?php
/**
 * @package Regime
 */
namespace Regime\Containers;

use Regime\Interfaces\IAdminMenuPage;
use Regime\Exceptions\AdminMenuPageException;
use Regime\Exceptions\ErrorsList;

/**
 * Admin menu page props container class.
 * @since 0.0.5
 */
class AdminMenuPage implements IAdminMenuPage
{

    /**
     * @var string $view
     * View file path.
     * @since 0.0.5
     */
    protected $view;

    /**
     * @var string $menu_title
     * Page menu title.
     * @since 0.0.5
     */
    protected $menu_title;

    /**
     * @var string $page_title
     * Page title.
     * @since 0.0.5
     */
    protected $page_title;

    /**
     * @var string $slug
     * Page slug.
     * @since 0.0.5
     */
    protected $slug;

    /**
     * @var string $icon
     * Icon file path.
     * @since 0.0.5
     */
    protected $icon = '';

    /**
     * @param string $view
     * View file path.
     * 
     * @param string $menu_title
     * Page menu title.
     * 
     * @param string $page_title
     * Page title.
     * 
     * @param string $slug
     * Page slug.
     * 
     * @throws Regime\Exceptions\AdminMenuPageException
     */
    public function __construct(string $view, string $menu_title, string $page_title, string $slug)
    {
        
        $ex = '';

        if (substr($view, -4) !== '.php') $ex = '-11';

        if (empty($ex) && !file_exists($view)) $ex = '-12';

        if (empty($ex) && empty($menu_title)) $ex = '-13';

        if (empty($ex) && empty($page_title)) $ex = '-14';

        if (empty($ex) && empty($slug)) $ex = '-15';

        if (!empty($ex)) throw new AdminMenuPageException(
            ErrorsList::ADMIN_MENU_PAGE[$ex]['message'],
            ErrorsList::ADMIN_MENU_PAGE[$ex]['code']
        );

        $this->view = $view;

        $this->menu_title = $menu_title;

        $this->page_title = $page_title;

        $this->slug = $slug;

    }

    /**
     * @since 0.0.5
     */
    public function viewGet() : string
    {

        return $this->view;

    }

    /**
     * @since 0.0.5
     */
    public function menuTitleGet() : string
    {

        return $this->menu_title;

    }

    /**
     * @since 0.0.5
     */
    public function pageTitleGet() : string
    {

        return $this->page_title;

    }

    /**
     * @since 0.0.5
     */
    public function slugGet() : string
    {

        return $this->slug;

    }

    /**
     * Set icon file.
     * @since 0.0.5
     * 
     * @param string $icon
     * Icon file URL.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\AdminMenuPageException
     */
    public function iconSet(string $icon) : self
    {

        if (!is_file($icon)) throw new AdminMenuPageException(
            ErrorsList::ADMIN_MENU_PAGE['-16']['message'],
            ErrorsList::ADMIN_MENU_PAGE['-16']['code']
        );

        $this->icon = $icon;

        return $this;

    }

    /**
     * Get icon file path.
     * 
     * @return string
     */
    public function iconGet() : string
    {

        return $this->icon;

    }

}
