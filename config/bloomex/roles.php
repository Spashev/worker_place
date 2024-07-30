<?php
/*
|--------------------------------------------------------------------------
| Application Name
|--------------------------------------------------------------------------
|
| List of roles that can be managed by personnel who have a specific role
|
*/
return [
    'Super admin' => ['Super admin', 'Admin', 'Warehouse manager', 'Packer'],
    'Admin' => ['Admin', 'Warehouse manager', 'Packer'],
    'Warehouse manager' => ['Warehouse manager', 'Packer'],
    'Packer' => [],
];
