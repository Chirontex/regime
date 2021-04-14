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
    const COMMON = [];

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

}
