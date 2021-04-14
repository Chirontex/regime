<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Containers\AdminMenuPage;

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
     * @var string $icons_path
     * Icon universal dir/root relative path.
     * @since 0.0.6
     */
    protected $icons_path = 'misc/icons/';

    /**
     * @since 0.0.3
     */
    protected function init() : self
    {

        $this->admin_pages_dir = $this->path.'admin/';

        $this->menuAdd();

        $forms_container = new AdminMenuPage(
            $this->admin_pages_dir.'forms.php',
            file_get_contents($this->path.$this->icons_path.'grid.svg').
                ' '.esc_html__('Формы', 'regime-ru_RU'),
            esc_html__('Формы', 'regime-ru_RU'),
            'regime-forms'
        );

        new Forms($this->path, $this->url, $forms_container);
        
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
                $this->url.$this->icons_path.'airplay.svg'
            );

        });

        return $this;

    }

}
