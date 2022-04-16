<?php
$this->layout('auth::layouts/admin', array_merge($this->data, [
    'is_logged' => $is_logged,
]));
?>

<h2 class="text-3xl mb-10">Dashboard</h2>

