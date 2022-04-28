<?php

function checkPermission(string $email, array $permission): array
{
    $CI = &get_instance();
    $getRolePermission = $CI->db
        ->select('rp.roleCode')
        ->join('permission p', 'p.permissionCode=rp.permissionCode')
        ->where_in('p.permission', $permission)
        ->where('rp.deleteAt', NULL)
        ->get('role_permission rp')
        ->result_array();
    $getRolePermission = array_values(array_unique(array_values(array_column($getRolePermission, 'roleCode'))));

    $getUserRole = $CI->db
        ->select('ru.roleCode')
        ->join('user u', 'u.userCode=ru.userCode')
        ->where('u.email', $email)
        ->where('ru.deleteAt', NULL)
        ->get('role_user ru')
        ->result_array();

    foreach ($getUserRole as $k => $v) {
        if (in_array($v['roleCode'], $getRolePermission)) {
            return [
                'status' => true,
            ];
        }
    }

    $getUserPermission = $CI->db
        ->select('up.upCode')
        ->join('user u', 'u.userCode=up.userCode')
        ->join('permission p', 'p.permissionCode=up.permissionCode')
        ->where_in('p.permission', $permission)
        ->get_where('user_permission up', ['u.email' => $email, 'up.deleteAt' => NULL])->result_array();
    if ($getUserPermission !=  NULL) {
        return [
            'status' => true,
        ];
    }
    return [
        'status' => false,
        'data' => formatResponse(200, [], [], 'You do not have access to this process', [], '')
    ];
}

function filename_safe($name)
{
    $except = array('\\', '/', ':', '*', '?', '"', '<', '>', '|');
    return str_replace($except, '', $name);
}
