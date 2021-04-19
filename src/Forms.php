<?php
/**
 * @package Regime
 */
namespace Regime;

/**
 * @final
 * Forms admin page POE.
 * @since 0.0.6
 */
final class Forms extends AdminPage
{

    /**
     * @since 0.1.4
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) $this->enqueueInit();

        return $this;

    }

    /**
     * Initialize styles and scripts enqueue.
     * @since 0.1.4
     * 
     * @return $this
     */
    protected function enqueueInit() : self
    {

        add_action('admin_enqueue_scripts', function() {

            if ($_GET['faction'] === 'edit') {

                wp_enqueue_script(
                    'form-edit',
                    $this->url.'js/form-edit.js',
                    [],
                    '0.4.2',
                    true
                );

            }

        });

        return $this;

    }

}
