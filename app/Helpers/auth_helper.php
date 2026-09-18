<?php

if (!function_exists('get_user')) {
    function get_user($request): array {
        // DEVELOPMENT MODE
        if (ENVIRONMENT === 'development') {
            return [
                'id'       => env('DEV_USER_ID', 1),
                'fullname' => env('DEV_USER_NAME', 'Developer'),
                'email'    => env('DEV_USER_EMAIL', 'dev@mail.com'),
                'opd_id'   => env('DEV_USER_OPD_ID', 1),
                'opd'      => env('DEV_USER_OPD', 'Diskominfo'),
                'role'     => env('DEV_USER_ROLE', 'admin'),
            ];
        }

        // PRODUCTION MODE
        $slo = service('slo');
        $userSlo = $slo->me();

        $user = model('User')
            ->select('
                user.*,
                opd.id as opd_id,
                opd.nama_opd as opd,
                role.id as role_id,
                role.slug_role as role
            ')
            ->join('opd', 'opd.id = user.opd_id')
            ->join('role', 'role.id = user.role_id')
            ->where('email', $userSlo->email)
            ->first();

        if (!$user) {
            throw new \RuntimeException('User tidak ditemukan');
        }

        return [
            'id' => $user->id,
            'fullname' => $userSlo->fullname,
            'email' => $user->email,
            'opd_id' => $user->opd_id,
            'opd' => $user->opd,
            'role' => $user->role
        ];
    }
}
