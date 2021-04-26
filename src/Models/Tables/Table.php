<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Interfaces\ITableProps;
use Regime\Exceptions\TableException;
use Regime\Exceptions\ErrorsList;
use Regime\Exceptions\TablePropsException;
use Regime\Interfaces\ITable;
use wpdb;

/**
 * @abstract
 * Abstract database table class.
 * @since 0.3.6
 */
abstract class Table implements ITable
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
     * @since 0.3.6
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
            "CREATE TABLE IF NOT EXISTS `".$this->wpdb->prefix.
                    $this->table_props->getTableName()."` (
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT".$fields.",
                PRIMARY KEY (`id`)".$indexes."
            )
            COLLATE='utf8mb4_unicode_ci'
            AUTO_INCREMENT=0"
        ) === false) throw new TableException(
            ErrorsList::TABLE['-30']['message'],
            ErrorsList::TABLE['-30']['code']
        );

        $this->init();

    }

    /**
     * Fires after object was created.
     * @since 0.5.4
     * 
     * @return $this
     */
    protected function init() : self
    {

        return $this;

    }

    /**
     * @since 0.3.7
     */
    public function entryAdd(array $values) : self
    {

        $values = $this->valuesPrepare($values);

        if ($this->wpdb->insert(
                $this->wpdb->prefix.$this->table_props->getTableName(),
                $values['values'],
                $values['formats']
            ) === false) throw new TableException(
            ErrorsList::TABLE['-31']['message'],
            ErrorsList::TABLE['-31']['code']
        );
        
        return $this;

    }

    /**
     * @since 0.4.0
     */
    public function entryUpdate(array $values, array $conditions) : self
    {

        $values = $this->valuesPrepare($values);

        $conditions = $this->valuesPrepare($conditions);

        if ($this->wpdb->update(
                $this->wpdb->prefix.$this->table_props->getTableName(),
                $values['values'],
                $conditions['values'],
                $values['formats'],
                $conditions['formats']
            ) === false) throw new TableException(
            ErrorsList::TABLE['-33']['message'],
            ErrorsList::TABLE['-33']['code']
        );

        return $this;

    }

    /**
     * @since 0.4.0
     */
    public function entryDelete(array $conditions, bool $strict = true) : self
    {

        $conditions = $this->valuesPrepare($conditions);

        $delete = $this->wpdb->delete(
            $this->wpdb->prefix.$this->table_props->getTableName(),
            $conditions['values'],
            $conditions['formats']
        );

        if ($strict) {

            if (empty($delete)) throw new TableException(
                ErrorsList::TABLE['-34']['message'],
                ErrorsList::TABLE['-34']['code']
            );

        }

        return $this;

    }

    /**
     * @since 0.4.2
     */
    public function selectAll() : array
    {

        $result = $this->wpdb->get_results(
            "SELECT *
                FROM `".$this->wpdb->prefix.
                    $this->table_props->getTableName()."`",
            ARRAY_A
        );

        if (!is_array($result)) throw new TablePropsException(
            ErrorsList::TABLE['-35']['message'],
            ErrorsList::TABLE['-35']['code']
        );

        return $result;

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
