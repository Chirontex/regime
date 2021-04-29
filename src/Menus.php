<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\MenusTable;

/**
 * @final
 * Menus admin page class.
 * @since 0.7.9
 */
final class Menus extends AdminPage
{

    /**
     * @var Regime\Models\Tables\MenusTable $table
     * Table object.
     * @since 0.7.9
     */
    protected $table;

    /**
     * @since 0.7.9
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new MenusTable(
                $this->wpdb,
                $this->table_props
            );

            $this->menusOutput();

        }

        return $this;

    }

    /**
     * Output menus.
     * @since 0.7.9
     * 
     * @return $this
     */
    protected function menusOutput() : self
    {

        add_filter('regime-menus', function() {

            return $this->wpdb->get_results(
                "SELECT t.term_id, t1.name
                    FROM `".$this->wpdb->prefix."term_taxonomy` AS t
                    LEFT JOIN `".$this->wpdb->prefix."terms` AS t1
                    ON t.term_id = t1.term_id
                    WHERE t.taxonomy = 'nav_menu'",
                ARRAY_A
            );

        });

        return $this;

    }

}
