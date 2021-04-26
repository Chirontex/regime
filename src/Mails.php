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
     * @var array $mails
     * Mail templates content.
     * @since 0.5.7
     */
    protected $mails;

    /**
     * @since 0.5.3
     */
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new MailsTable(
                $this->wpdb,
                $this->table_props
            );

            $this->mails = $this->table->mailsGetAll();

            $this->filters();

        }
        
        return $this;

    }

    /**
     * Add mail templates filters.
     * @since 0.5.7
     * 
     * @return $this
     */
    protected function filters() : self
    {

        add_filter('regime-mail-registration-header', function() {

            return htmlspecialchars(
                $this->mails['registration']['header']
            );

        });

        add_filter('regime-mail-registration-message', function() {

            return htmlspecialchars(
                $this->mails['registration']['message']
            );

        });

        add_filter('regime-mail-password-header', function() {

            return htmlspecialchars(
                $this->mails['password']['header']
            );

        });

        add_filter('regime-mail-password-message', function() {

            return htmlspecialchars(
                $this->mails['password']['message']
            );

        });

        return $this;

    }

}
