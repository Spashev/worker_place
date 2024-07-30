<?php

namespace App\Helpers;

class ErrorCode
{
    const ACCESS_FAILED = 'access_failed';
    const NOT_FOUND = 'not_found';
    const USER_EXISTS = 'user_exists';
    const WRONG_WAREHOUSE = 'wrong_warehouse';
    const ORDER_NOT_FOUND = 'order_not_found';
    const ERROR_SAVE_IMAGE = 'error_save_image';
    const ERROR_SAVE_HISTORY = 'error_save_history';
    const ERROR_REMOVED_IMAGE = 'error_removed_image';
    const WRONG_ROLES = 'wrong_roles';
    const WRONG_STATUS = 'wrong_status';
}