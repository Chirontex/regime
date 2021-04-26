<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Interfaces\IAdminMenuPage;
use Regime\Interfaces\ITableProps;

/**
 * @abstract
 * Admin page abstract POE class.
 * @since 0.0.3
 */
abstract class AdminPage extends PointOfEntry
{

    /**
     * @var array $notice_container
     * Notice container. Contains notice type and text.
     * @since 0.0.3
     */
    protected $notice_container = [
        'type' => '',
        'text' => ''
    ];

    /**
     * @var IAdminMenuPage $container
     * Admin menu page props container obj.
     * @since 0.0.5
     */
    protected $container;

    /**
     * @var string $notice_fail_message
     * Typical verifying nonce fail message.
     * @since 0.4.1
     */
    protected $nonce_fail_message = '';

    /**
     * @var ITableProps $table_props
     * Table properties container.
     * @since 0.5.3
     */
    protected $table_props;

    /**
     * @var \Regime\Interfaces\ITable $table
     * Table class object.
     * @since 0.5.6
     */
    protected $table;

    /**
     * @since 0.0.4
     * 
     * @param string $path
     * Plugin root directory.
     * 
     * @param string $url
     * Plugin root directory as URL.
     * 
     * @param IAdminMenuPage $container
     * Admin menu page props container obj.
     * 
     * @param ITableProps $table_props
     * Table properties container.
     * @since 0.5.3
     */
    public function __construct(string $path, string $url, IAdminMenuPage $container, ITableProps $table_props)
    {

        $this->nonce_fail_message = esc_html__(
            'Произошла ошибка при сохранении формы. Скорее всего, это произошло потому, что страница была загружена слишком давно. Пожалуйста, попробуйте ещё раз.',
            'regime'
        );
        
        $this->container = $container;

        $this->table_props = $table_props;

        parent::__construct($path, $url);

        $this->menuAddPage();
        
        if ($_GET['page'] ===
            $this->container->slugGet()) $this
            ->bootstrapAdd()
            ->commonEnqueueInit();

    }

    /**
     * Add page to menu.
     * @since 0.0.4
     * 
     * @return $this
     */
    protected function menuAddPage() : self
    {

        add_action('admin_menu', function() {

            add_submenu_page(
                'regime',
                $this->container->pageTitleGet(),
                $this->container->menuTitleGet(),
                8,
                $this->container->slugGet(),
                function() {

                    $path = $this->path;
                    $url = $this->url;

                    do_action('regime-admin-page-notice');

                    require_once $this->container->viewGet();

                }
            );

        });

        return $this;

    }

    /**
     * Plug Bootstrap to the page.
     * @since 0.0.6
     * 
     * @return $this
     */
    protected function bootstrapAdd() : self
    {

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'bootstrap-min',
                file_exists($this->path.'css/bootstrap-5.0.0-beta3/bootstrap.min.css') ?
                    $this->url.'css/bootstrap-5.0.0-beta3/bootstrap.min.css' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css',
                [],
                '5.0.0-beta3'
            );

            wp_enqueue_script(
                'bootstrap-min',
                file_exists($this->path.'js/bootstrap-5.0.0-beta3/bootstrap.bundle.min.js') ?
                    $this->url.'js/bootstrap-5.0.0-beta3/bootstrap.bundle.min.js' :
                    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js',
                [],
                '5.0.0-beta3'
            );

        });

        return $this;

    }

    /**
     * Initiate common styles and scripts enqueue.
     * @since 0.5.6
     * 
     * @return $this
     */
    protected function commonEnqueueInit() : self
    {

        add_action('admin_enqueue_scripts', function() {

            wp_enqueue_style(
                'regime-common',
                $this->url.'css/common.css',
                [],
                '0.2.0'
            );

        });

        return $this;

    }

    /**
     * Output notice on admin page.
     * @since 0.0.3
     * 
     * @param string $type
     * Available types: 'success', 'warning', 'error' (or 'danger').
     * 
     * @param string $text
     * Notice text.
     * 
     * @return $this
     */
    protected function notice(string $type, string $text) : self
    {

        if ($type === 'danger') $type = 'error';

        $this->notice_container = [
            'type' => $type,
            'text' => $text
        ];

        add_action('regime-admin-page-notice', function() {

            ob_start();

?>
<div class="notice notice-<?= htmlspecialchars($this->notice_container['type']) ?> is-dismissible" style="max-width: 500px; margin: 1rem auto;">
    <p style="text-align: center;"><?= $this->notice_container['text'] ?></p>
</div>
<?php

            echo ob_get_clean();

        });

        return $this;

    }

}
