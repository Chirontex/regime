<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\MenusTable;

/**
 * @final
 * Menus global handler.
 * @since 0.8.1
 */
final class MenusHandler extends GlobalHandler
{

    /**
     * @since 0.8.1
     */
    protected function init(): self
    {

        add_action('plugins_loaded', function() {

            $this->menuReplace();

        });
        
        return $this;

    }

    /**
     * Replace menu for user by his role.
     * @since 0.8.1
     * 
     * @return $this
     */
    protected function menuReplace() : self
    {

        add_filter('wp_nav_menu_args', function($args) {

            $user = get_current_user_id();

            if ($user !== 0 && $args['theme_location'] === 'primary') {

                $menus_table = new MenusTable(
                    $this->wpdb,
                    $this->tables_props['menus']
                );

                $menu_id = $menus_table->getRoleMenu('user');

                if ($menu_id !== 0) $args['menu'] = $menu_id;

            }

            return $args;

        });

        return $this;

    }

}
