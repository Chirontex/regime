<?php
/**
 * @package Regime
 */
namespace Regime\Exceptions;

/**
 * Contains all exceptions codes and messages.
 * @since 0.0.5
 */
class ErrorsList
{

    /**
     * Common exceptions.
     * @since 0.0.5
     */
    const COMMON = [
        '-1' => [
            'message' => '%1$s cannot be empty.',
            'code' => -1
        ],
        '-2' => [
            'message' => '%1$s cannot be less than 1.',
            'code' => -2
        ],
        '-3' => [
            'message' => '%1$s cannot be less than %2$d.',
            'code' => -2
        ],
        '-4' => [
            'message' => '%1$s must be %2$s.',
            'code' => -4
        ],
        '-5' => [
            'message' => '%1$s not found.',
            'code' => -5
        ]
    ];

    /**
     * Admin menu page container exceptions.
     * @since 0.0.5
     */
    const ADMIN_MENU_PAGE = [
        '-11' => [
            'message' => 'Invalid view.',
            'code' => -11
        ],
        '-12' => [
            'message' => 'View file not exist.',
            'code' => -12
        ],
        '-13' => [
            'message' => 'Menu title cannot be empty.',
            'code' => -13
        ],
        '-14' => [
            'message' => 'Page title cannot be empty.',
            'code' => -14,
        ],
        '-15' => [
            'message' => 'Slug cannot be empty.',
            'code' => -15
        ],
        '-16' => [
            'message' => 'Icon file not found.',
            'code' => -16
        ]
    ];

    const TABLE_PROPS = [
        '-20' => [
            'message' => 'Key cannot be empty.',
            'code' => -20
        ],
        '-21' => [
            'message' => 'Params cannot be empty.',
            'code' => -21
        ],
        '-22' => [
            'message' => 'Invalid entity type.',
            'code' => -22
        ],
        '-23' => [
            'message' => 'Invalid entity.',
            'code' => -23
        ],
        '-24' => [
            'message' => 'Table name cannot be empty.',
            'code' => -24
        ]
    ];

    const TABLE = [
        '-30' => [
            'message' => 'Table creation failure.',
            'code' => -30
        ],
        '-31' => [
            'message' => 'Entry insertion failure.',
            'code' => -31
        ],
        '-32' => [
            'message' => 'Field does not exist in this table.',
            'code' => -32
        ],
        '-33' => [
            'message' => 'Entry updating failure.',
            'code' => -33
        ],
        '-34' => [
            'message' => 'Entry deleting failure.',
            'code' => -34
        ],
        '-35' => [
            'message' => 'Data selecting failure.',
            'code ' => -35
        ],
        '-36' => [
            'message' => 'Entry not found.',
            'code' => -36
        ]
    ];
    
    const FORMS_TABLE = [
        '-40' => [
            'message' => 'Form deleting failure.',
            'code' => -40
        ]
    ];

    const GLOBAL_HANDLER = [
        '-50' => [
            'message' => 'Tables properties list may content only ITableProps objects.',
            'code' => -50
        ]
    ];

}
