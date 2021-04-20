<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Interfaces\IAdminMenuPage;

/**
 * @abstract
 * Admin page abstract POE class.
 * @since 0.0.3
 */
abstract class AdminPage extends PointOfEntry
{

    /**
     * @var array $notice
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
     */
    public function __construct(string $path, string $url, IAdminMenuPage $container)
    {
        
        $this->container = $container;

        parent::__construct($path, $url);

        $this->menuAddPage();
        
        if ($_GET['page'] ===
            $this->container->slugGet()) $this->bootstrapAdd();

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
