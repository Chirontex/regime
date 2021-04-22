<?php
/**
 * @package Regime
 */
namespace Regime\Models\Tables;

use Regime\Exceptions\FormsTableException;
use Regime\Exceptions\TableException;
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

        try {

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
                ])
                ->entryAdd([
                    'form_id' => $id,
                    'key' => 'status',
                    'value' => 'active'
                ]);

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        return $this;

    }

    /**
     * Update form fields.
     * @since 0.4.0
     * 
     * @param int $form_id
     * Form ID.
     * 
     * @param string $fields
     * Form fields in JSON format.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function formUpdate(int $form_id, string $fields) : self
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        if (empty($fields)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Fields'),
            ErrorsList::COMMON['-1']['code']
        );

        try {

            $this->entryUpdate(
                ['value' => $fields],
                [
                    'form_id' => $form_id,
                    'key' => 'fields'
                ]
            );

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

        return $this;

    }

    /**
     * Update form status.
     * @since 0.4.0
     * 
     * @param int $form_id
     * Form ID.
     * 
     * @param string $status
     * Form status.
     * 
     * @return $this
     * 
     * @throws Regime\Exceptions\FormsTableException
     */
    public function formUpdateStatus(int $form_id, string $status) : self
    {

        if ($form_id < 1) throw new FormsTableException(
            ErrorsList::COMMON['-2']['message'],
            ErrorsList::COMMON['-2']['code']
        );

        if (empty($status)) throw new FormsTableException(
            sprintf(ErrorsList::COMMON['-1']['message'], 'Status'),
            ErrorsList::COMMON['-1']['code']
        );

        try {

            $this->entryUpdate(
                ['value' => $status],
                [
                    'form_id' => $form_id,
                    'key' => 'status'
                ]
            );

        } catch (TableException $e) {

            throw new FormsTableException(
                $e->getMessage(),
                $e->getCode()
            );

        }

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
