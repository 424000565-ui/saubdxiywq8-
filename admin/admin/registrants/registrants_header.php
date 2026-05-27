<?php
// Calculate total count or fallback gracefully to 0
$display_total = isset($registrants) ? count($registrants) : 0;
?>

<link rel="stylesheet" href="../../css/style.css">

<div class="custom-page-header">
    <div class="custom-page-title-group">
        <span class="custom-panel-subtitle">Admin Panel</span>
        <h2 class="custom-panel-title">Registrant Viewer</h2>
    </div>
    
    <div class="custom-badge-count">
        <?= $display_total ?> registrant<?= $display_total !== 1 ? 's' : '' ?>
    </div>
</div>