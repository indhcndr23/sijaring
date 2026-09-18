<?php

namespace App\Libraries;

class Auth
{
    private $user = null;

    public function setUser($user): void
    {
        $this->user = $user;
    }

    public function user()
    {
        return $this->user;
    }

    public function check(): bool
    {
        return $this->user !== null;
    }

    public function hasRole($roles): bool
    {
        if (!$this->user) return false;

        $userRole = $this->user['role'] ?? null;

        if ($roles === '*') {
            return true;
        }

        if (is_array($roles)) {
            if (in_array('*', $roles)) {
                return true;
            }
            return in_array($userRole, $roles);
        }

        return $userRole === $roles;
    }
}
