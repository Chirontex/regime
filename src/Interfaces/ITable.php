<?php
/**
 * @package Regime
 */
namespace Regime\Interfaces;

use wpdb;

interface ITable
{

    /**
     * Table constructor.
     * @since 0.5.6
     * 
     * @param wpdb $wpdb
     * WP database object.
     * 
     * @param ITableProps $table_props
     * Table properties.
     */
    public function __construct(wpdb $wpdb, ITableProps $table_props);

    /**
     * Insert the entry in the table.
     * @since 0.5.6
     * 
     * @param array $values
     * Associative array. Format: column => value.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\TableException
     */
    public function entryAdd(array $values) : self;

    /**
     * Update an entry.
     * @since 0.5.6
     * 
     * @param array $values
     * Associative array. Format: column => value.
     * 
     * @param array $conditions
     * Values in the WHERE part united with AND.
     * Associative array. Format: column => value.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\TableException
     */
    public function entryUpdate(array $values, array $conditions) : self;

    /**
     * Delete an entry (entries).
     * @since 0.5.6
     * 
     * @param array $conditions
     * Values in the WHERE part united with AND.
     * Associative array. Format: column => value.
     * 
     * @param bool $strict
     * Determines whether the method will throw an exception
     * if nothing is removed. 
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\TableException
     */
    public function entryDelete(array $conditions, bool $strict = true) : self;

    /**
     * Select all entries from table.
     * @since 0.5.6
     * 
     * @return array
     * 
     * @throws Regime\Exceptions\TableException
     */
    public function selectAll() : array;

}