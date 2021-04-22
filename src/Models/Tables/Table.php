<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Interfaces\ITableProps;
use Regime\Exceptions\TableException;
use Regime\Exceptions\ErrorsList;
use wpdb;

/**
 * @abstract
 * Abstract database table class.
 * @since 0.3.6
 */
abstract class Table
{

    /**
     * @var wpdb $wpdb
     * WP database object.
     * @since 0.3.6
     */
    protected $wpdb;

    /**
     * @var ITableProps $table_props
     * Table properties.
     * @since 0.3.6
     */
    protected $table_props;

    /**
     * Table constructor.
     * @since 0.3.6
     * 
     * @param wpdb $wpdb
     * WP database object.
     * 
     * @param ITableProps $table_props
     * Table properties.
     */
    public function __construct(wpdb $wpdb, ITableProps $table_props)
    {
        
        $this->wpdb = $wpdb;

        $this->table_props = $table_props;

        $fn = function(array $entities, string $type) {

            $format = '';

            $result = "";

            switch ($type) {

                case 'fields':
                    $format = "`%1\$s` %2\$s";
                    break;

                case 'indexes':
                    $format = "%2\$s (`%1\$s`)";
                    break;

            }

            foreach ($entities as $key => $params) {

                $result .= ", ".sprintf($format, $key, $params);

            }

            return $result;

        };

        $fields = call_user_func(
            $fn,
            $this->table_props->getFields(),
            'fields'
        );

        $indexes = call_user_func(
            $fn,
            $this->table_props->getIndexes(),
            'indexes'
        );

        if ($this->wpdb->query(
            "CREATE TABLE IF NOT EXISTS `.$this->wpdb->prefix.
                    $this->table_props->getTableName.` (
                `id` BIGINT NOT NULL AUTO_INCREMENT".$fields.",
                PRIMARY KEY (`id`)".$indexes."
            )
            COLLATE='".DB_CHARSET."'
            AUTO_INCREMENT=0"
        ) === false) throw new TableException(
            ErrorsList::TABLE['-30']['message'],
            ErrorsList::TABLE['-30']['code']
        );

    }

    /**
     * Insert the entry in the table.
     * @since 0.3.7
     * 
     * @param array $values
     * Associative array. Format: column => value.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\TableException
     */
    public function entryAdd(array $values) : self
    {

        $prepared = $this->valuesPrepare($values);

        if ($this->wpdb->insert(
                $this->wpdb->prefix.$this->table_props->getTableName(),
                $prepared['values'],
                $prepared['formats']
            ) === false) throw new TableException(
            ErrorsList::TABLE['-31']['message'],
            ErrorsList::TABLE['-31']['code']
        );
        
        return $this;

    }

    /**
     * Preparing values and defines formats.
     * @since 0.3.8
     * 
     * @param array $values
     * 
     * @return array
     */
    protected function valuesPrepare(array $values) : array
    {

        $result = [
            'values' => [],
            'formats' => []
        ];

        $fields = $this->table_props->getFields();

        foreach ($values as $field => $value) {

            if (!isset($fields[$field])) throw new TableException(
                ErrorsList::TABLE['-32']['message'],
                ErrorsList::TABLE['-32']['code']
            );

            $format = '%s';

            if (is_int($value)) $format = '%d';

            if (is_float($value)) $format = '%f';

            if ($format === '%s') $values[$field] = (string)$value;

            $result['formats'][] = $format;

        }

        $result['values'] = $values;

        return $result;

    }

}
