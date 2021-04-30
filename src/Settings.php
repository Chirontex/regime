<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\SettingsTable;

/**
 * @final
 * Settings admin page class.
 * @since 0.8.5
 */
final class Settings extends AdminPage
{

    /**
     * @var Regime\Models\Tables\SettingsTable $table
     * Table object.
     * @since 0.8.5
     */
    protected $table;

    /**
     * @since 0.8.5
     */
    protected function init(): self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new SettingsTable(
                $this->wpdb,
                $this->table_props
            );

        }
        
        return $this;

    }

}
