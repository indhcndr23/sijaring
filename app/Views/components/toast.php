<?php
if ($toast = session()->getFlashdata('toast')):
    $allowedTypes = ['success', 'error', 'warning', 'info'];
    $type = $toast['type'] ?? 'info';

    if (!in_array($type, $allowedTypes)) {
        $type = 'info';
    }
?>
<script>
    toastr.<?= $type ?>("<?= esc($toast['message']) ?>");
</script>
<?php endif; ?>
