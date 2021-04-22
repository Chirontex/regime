<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Containers\AdminMenuPage;
use Regime\Containers\TableProps;

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
     * @var TableProps $forms_table_props
     * Forms table properties container.
     * @since 0.4.4
     */
    protected $forms_table_props;

    /**
     * @since 0.0.3
     */
    protected function init() : self
    {

        $this->admin_pages_dir = $this->path.'admin/';

        $this->forms_table_props = new TableProps('regime_forms');

        $this->forms_table_props
            ->setField('form_id', 'BIGINT(20) UNSIGNED NOT NULL')
            ->setField('key', 'VARCHAR(255) NOT NULL')
            ->setField('value', 'LONGTEXT NOT NULL');

        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) {

            $this
                ->menuAdd()
                ->formsInit()
                ->submenuRemove();

        }
        
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
                esc_html__('Regime', 'regime'),
                esc_html__('Regime', 'regime'),
                100,
                'regime',
                function() {},
                $this->url.$this->icons_path.'airplay.svg'
            );

        });

        return $this;

    }

    /**
     * Initialize forms page.
     * @since 0.1.0
     * 
     * @return $this
     */
    protected function formsInit() : self
    {

        $view = 'forms.php';

        $page_title = esc_html__('Формы', 'regime');

        if (isset($_GET['faction'])) {

            if ($_GET['faction'] === 'edit') {

                $view = 'form-edit.php';

                $page_title = esc_html__('Новая форма | Regime', 'regime');

                if (isset($_GET['fid'])) $page_title = empty($_GET['fid']) ?
                    $page_title : esc_html__('Редактирование формы | Regime', 'regime');

            }

        }

        $forms_container = new AdminMenuPage(
            $this->admin_pages_dir.$view,
            file_get_contents($this->path.$this->icons_path.'grid.svg').
                ' '.esc_html__('Формы', 'regime'),
            $page_title,
            'regime-forms'
        );

        new Forms(
            $this->path,
            $this->url,
            $forms_container,
            $this->forms_table_props
        );

        return $this;

    }

    /**
     * Remove main page from its own submenu.
     * @since 0.0.7
     * 
     * @return $this
     */
    protected function submenuRemove() : self
    {

        add_action('admin_menu', function() {

            remove_submenu_page('regime', 'regime');

        });

        return $this;

    }

}
