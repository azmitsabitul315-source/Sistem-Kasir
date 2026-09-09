<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-name" content="<?= csrf_token() ?>">
    <meta name="csrf-hash" content="<?= csrf_hash() ?>">
    <title><?= esc($title ?? 'POS Warung Ayam Bakar') ?> — POS Ayam Bakar</title>
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('css/print.css') ?>">
</head>
<body>
<div class="app-wrapper">
    <?= $this->include('layout/sidebar') ?>

    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar no-print">
            <h1><?= esc($title ?? 'Dashboard') ?></h1>
            <div class="topbar-info">
                <span id="topbar-date"></span>
                <span class="clock" id="topbar-clock"></span>
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">
