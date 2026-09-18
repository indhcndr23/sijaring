<?php

if (!function_exists('success')) {
    function success($msg): array {
        return ['type' => 'success', 'message' => $msg];
    }
}

if (!function_exists('info')) {
    function info($msg): array {
        return ['type' => 'success', 'message' => $msg];
    }
}

if (!function_exists('error')) {
    function error($msg): array {
        return ['type' => 'error', 'message' => $msg];
    }
}

if (!function_exists('warning')) {
    function warning($msg): array {
        return ['type' => 'warning', 'message' => $msg];
    }
}
