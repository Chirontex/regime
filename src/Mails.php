<?php
/**
 * @package Regime
 */
namespace Regime;

use Regime\Models\Tables\MailsTable;

/**
 * @final
 * Mails admin page class.
 * @since 0.5.3
 */
final class Mails extends AdminPage
{

    /**
     * @var MailsTable $mails_table
     * Class to work with DB table.
     * @since 0.5.6
     */
    protected $table;

    /**
     * @since 0.5.3
     */
    protected function init(): self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new MailsTable(
                $this->wpdb,
                $this->table_props
            );

        }
        
        return $this;

    }

}
