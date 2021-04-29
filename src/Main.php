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
     * @var TableProps $mails_table_props
     * Letters table properties container.
     * @since 0.5.3
     */
    protected $mails_table_props;

    /**
     * @var TableProps $menus_table_props
     * Menus setting table properties container.
     * @since 0.7.8
     */
    protected $menus_table_props;

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
            ->setField('value', 'LONGTEXT');

        $this->mails_table_props = new TableProps('regime_mails');

        $this->mails_table_props
            ->setField('template_id', 'CHAR(50) NOT NULL')
            ->setField('header', 'VARCHAR(255)')
            ->setField('message', 'LONGTEXT');

        $this->menus_table_props = new TableProps('regime_menus');

        $this->menus_table_props
            ->setField('role', 'CHAR(50) NOT NULL')
            ->setField('menu_id', 'BIGINT(20) UNSIGNED NOT NULL');

        if (strpos($_SERVER['REQUEST_URI'], 'wp-admin') !== false) {

            $this
                ->menuAdd()
                ->formsInit()
                ->mailsInit()
                ->menusInit()
                ->submenuRemove();

        }

        $this
            ->formsHandlerInit()
            ->menusHandlerInit()
            ->frontendShortcodesInit();
        
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

            if ($_GET['faction'] === 'edit' ||
                $_GET['faction'] === 'copy') {

                $view = 'form-edit.php';

                $page_title = esc_html__('Новая форма | Regime', 'regime');

                if (isset($_GET['fid'])) $page_title = empty($_GET['fid']) ?
                    $page_title : esc_html__('Редактирование формы | Regime', 'regime');
                elseif (!isset($_GET['ftype'])) $_GET['ftype'] = 'registration';

            }

        }

        $container = new AdminMenuPage(
            $this->admin_pages_dir.$view,
            file_get_contents($this->path.$this->icons_path.'grid.svg').
                ' '.esc_html__('Формы', 'regime'),
            $page_title,
            'regime-forms'
        );

        new Forms(
            $this->path,
            $this->url,
            $container,
            $this->forms_table_props
        );

        return $this;

    }

    /**
     * Initialize mail templates page.
     * @since 0.5.3
     * 
     * @return $this
     */
    protected function mailsInit() : self
    {

        $container = new AdminMenuPage(
            $this->admin_pages_dir.'mails.php',
            file_get_contents($this->path.$this->icons_path.'mail.svg').
            ' '.esc_html__('Шаблоны писем', 'regime'),
            'Шаблоны писем',
            'regime-letters'
        );

        new Mails(
            $this->path,
            $this->url,
            $container,
            $this->mails_table_props
        );

        return $this;

    }

    /**
     * Initialize menus admin page.
     * @since 0.7.9
     * 
     * @return $this
     */
    protected function menusInit() : self
    {

        $container = new AdminMenuPage(
            $this->admin_pages_dir.'menus.php',
            file_get_contents($this->path.$this->icons_path.'menu.svg').
            ' '.esc_html__('Настройки меню', 'regime'),
            'Настройки меню',
            'regime-menus'
        );

        new Menus(
            $this->path,
            $this->url,
            $container,
            $this->menus_table_props
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

    /**
     * Initialize frontend shorcodes.
     * @since 0.6.3
     * 
     * @return $this
     */
    protected function frontendShortcodesInit() : self
    {

        new FrontendShortcodes(
            $this->path,
            $this->url,
            ['forms' => $this->forms_table_props]
        );

        return $this;

    }

    /**
     * Initialize forms handler object.
     * @since 0.6.7
     * 
     * @return $this
     */
    protected function formsHandlerInit() : self
    {

        new FormsHandler(
            $this->path,
            $this->url,
            [
                'forms' => $this->forms_table_props,
                'mails' => $this->mails_table_props
            ]
        );

        return $this;

    }

    /**
     * Initialize menus handler object.
     * @since 0.8.1
     * 
     * @return $this
     */
    protected function menusHandlerInit() : self
    {

        new MenusHandler(
            $this->path,
            $this->url,
            ['menus' => $this->menus_table_props]
        );

        return $this;

    }

}
