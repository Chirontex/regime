<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\FormsTableException;
use Regime\Exceptions\ErrorsList;

/**
 * Forms table class.
 * @since 0.3.7
 */
class Forms extends Table
{

    /**
     * Save new form in the table.
     * @since 0.3.7
     * 
     * @param string $type
     * Form type.
     * 
     * @param string $fields
     * Form fields in JSON format.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function formAdd(string $type, string $fields) : self
    {

        $invalid_param = '';

        if (empty($type)) $invalid_param = 'Type';

        if (empty($fields)) $invalid_param = 'Fields';

        if (!empty($invalid_param)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], $invalid_param),
            ErrorsList::COMMON['-1']['code']
        );

        $id = $this->getNewFormId();

        $this
            ->entryAdd([
                'form_id' => $id,
                'key' => 'type',
                'value' => $type
            ])
            ->entryAdd([
                'form_id' => $id,
                'key' => 'fields',
                'value' => $fields
            ]);

        return $this;

    }

    /**
     * Return ID for the new form.
     * @since 0.3.7
     * 
     * @return int
     */
    protected function getNewFormId() : int
    {

        $id = 0;

        $select = $this->wpdb->get_results(
            "SELECT t.form_id
                FROM `".$this->wpdb->prefix.
                    $this->table_props->getTableName()."` AS t
                WHERE t.key = 'type'
                ORDER BY t.form_id DESC
                LIMIT 1",
            ARRAY_A
        );

        if (!empty($select)) $id = (int)$select[0]['form_id'];

        $id += 1;

        return $id;

    }

}
