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
            "CREATE TABLE `.$this->wpdb->prefix.
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

}
