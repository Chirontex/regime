<?php
/**
 * @package Regime
 */
namespace Regime;

/**
 * @final
 * Main POE.
 * @since 0.0.2
 */
final class Main extends PointOfEntry
{

    /**
     * @var string $admin_pages_dir
     * Admin pages directory.
     * @since 0.0.4
     */
    protected $admin_pages_dir;

    /**
     * @since 0.0.3
     */
    protected function init() : self
    {

        $this->admin_pages_dir = $this->path.'admin/';

        $this->menuAdd();
        
        return $this;

    }

    /**
     * Add plugin pages to admin menu.
     * @since 0.0.4
     * 
     * @return $this
     */
    protected function menuAdd() : self
    {

        add_action('admin_menu', function() {

            add_menu_page(
                esc_html__('Regime', 'regime-ru_RU'),
                esc_html__('Regime', 'regime-ru_RU'),
                8,
                'regime',
                function() {

                    echo 'test';

                },
                $this->url.'misc/icons/airplay.svg'
            );

        });

        return $this;

    }

}
