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
    protected function init() : self
    {

        if ($_GET['page'] ===
            $this->container->slugGet()) {

            $this->table = new SettingsTable(
                $this->wpdb,
                $this->table_props
            );

            if (isset($_POST['regimeSettings-wpnp'])) $this->settingsSave();

            $this->settingsOutput();

        }
        
        return $this;

    }

    /**
     * Output settings to the page.
     * @since 0.8.6
     * 
     * @return $this
     */
    protected function settingsOutput() : self
    {

        add_filter('regime-settings-sender-email', function() {

            return $this->table->get('sender_email');

        });

        add_filter('regime-settings-sender-name', function() {

            return $this->table->get('sender_name');

        });

        return $this;

    }

    /**
     * Save settings.
     * @since 0.8.6
     * 
     * @return $this
     */
    protected function settingsSave() : self
    {

        add_action('plugins_loaded', function() {

            if (wp_verify_nonce(
                $_POST['regimeSettings-wpnp'],
                'regimeSettings'
            ) === false) $this->notice(
                'danger',
                $this->nonce_fail_message
            );
            else {

                $this->table
                    ->set(
                        'sender_email',
                        (string)$_POST['regimeSettings_sender_email']
                    )
                    ->set(
                        'sender_name',
                        (string)$_POST['regimeSettings_sender_name']
                    );

                $this->notice(
                    'success',
                    esc_html__('Изменения сохранены!', 'regime')
                );

            }

        });

        return $this;

    }

}
