<?php

// includes/header.php 
include_once 'functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduSkill Marketplace - EMS</title>
    <!-- Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Custom Styles -->
    <link rel="stylesheet" href="/assets/css/Nikhil.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light shadow-sm sticky-top border-bottom border-success-subtle" style="background-color: #ecfdf5;">
    <div class="container">
        <a class="navbar-brand fw-bold fs-4" href="/index.php" style="color: var(--primary-color);">EMS.</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/index.php">Browse Courses</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown">
                            Hi, <?php echo $_SESSION['full_name']; ?> 
                            <span class="badge" style="background-color: var(--primary-color);"><?php echo $_SESSION['role']; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php if ($_SESSION['role'] == 'Admin'): ?>
                                <li><a class="dropdown-item" href="/admin/dashboard.php">Admin Panel</a></li>
                            <?php
    elseif ($_SESSION['role'] == 'Provider'): ?>
                                <li><a class="dropdown-item" href="/provider/dashboard.php">Provider Portal</a></li>
                            <?php
    else: ?>
                                <li><a class="dropdown-item" href="/learner/dashboard.php">My Account</a></li>
                            <?php
    endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/logout.php">Sign Out</a></li>
                        </ul>
                    </li>
                <?php
else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-primary btn-sm me-2" href="/login.php">Log In</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary-custom btn-sm text-white" href="/register.php">Sign Up</a>
                    </li>
                <?php
endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-5">
