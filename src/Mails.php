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
     * @var MailsTable $table
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

            if (isset($_POST['regimeMailsEdit-wpnp'])) $this->mailsSave();

            $this->getMails()->filters();

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

    /**
     * Save mail templates.
     * @since 0.5.8
     * 
     * @return $this
     */
    protected function mailsSave() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeMailsEdit-wpnp'],
                'regimeMailsEdit'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_message
            );
            else {

                $this->table
                    ->mailUpdate(
                        'registration',
                        (string)$_POST['regimeMailRegistrationHeader'],
                        (string)$_POST['regimeMailRegistrationMessage']
                    )
                    ->mailUpdate(
                        'password',
                        (string)$_POST['regimeMailPasswordHeader'],
                        (string)$_POST['regimeMailPasswordMessage']
                    );

                $this->notice(
                    'success',
                    esc_html__('Шаблоны сохранены!', 'regime')
                );

            }

        });

        return $this;

    }

    /**
     * Get mail templates content.
     * @since 0.5.8
     * 
     * @return $this
     */
    protected function getMails() : self
    {

        add_action('init', function() {

            $this->mails = $this->table->mailsGetAll();

        });

        return $this;

    }

}
