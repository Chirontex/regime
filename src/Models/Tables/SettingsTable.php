<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\SettingsTableException;
use Regime\Exceptions\ErrorsList;

/**
 * Settings table class.
 * @since 0.8.5
 */
class SettingsTable extends Table
{

    /**
     * @since 0.8.5
     */
    protected function init() : self
    {

        if (empty($this->selectAll())) {

            $this
                ->entryAdd([
                    'key' => 'sender_email',
                    'value' => 'wordpress@'.$_SERVER['HTTP_HOST']
                ])
                ->entryAdd([
                    'key' => 'sender_name',
                    'value' => 'Admin'
                ]);

        }
        
        return $this;

    }

    /**
     * Get setting.
     * @since 0.8.5
     * 
     * @param string $key
     * Setting key. Cannot be empty.
     * 
     * @return string
     * 
     * @throws Regime\Exceptions\SettingsTableException
     */
    public function get(string $key) : string
    {

        if (empty($key)) throw new SettingsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Key'),
            ErrorsList::COMMON['-1']['code']
        );

        return (string)$this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT t.value
                    FROM `".$this->wpdb->prefix.
                        $this->table_props->getTableName()."` AS t
                    WHERE t.key = %s",
                $key
            )
        );

    }

    /**
     * Set setting.
     * @since 0.8.5
     * 
     * @param string $key
     * Setting key. Cannot be empty.
     * 
     * @param string $value
     * Setting value.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\SettingsTableException
     */
    public function set(string $key, string $value) : self
    {

        if (empty($key)) throw new SettingsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Key'),
            ErrorsList::COMMON['-1']['code']
        );

        $this->entryUpdate(
            ['value' => $value],
            ['key' => $key]
        );

        return $this;

    }

}
