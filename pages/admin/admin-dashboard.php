<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: ../ZE-Electronics.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link rel="stylesheet" href="../../assets/css/admin/admin-style.css"/>
</head>
<body>

<div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
    <div class="profile">
        <img src="../../assets/images/avatar.jpg" alt="Admin"/>
        <span class="role">ADMIN</span>
    </div>

    <nav class="nav-menu">
        <ul>
        <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
        <li><a href="user-management.html"><i class="fas fa-users"></i> User Management</a></li>
        <li><a href="pending-approvals.html"><i class="fas fa-clock"></i> Pending Approvals</a></li>
        <li><a href="#"><i class="fas fa-download"></i> Export</a></li>
        <li><a href="activity-history.html"><i class="fas fa-history"></i> History</a></li>
        <ul>
    </nav>

    <a href="../../backend/auth/logout.php" class="logout-btn">LOGOUT</a>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
    <header>
        <h1>Admin Dashboard</h1>
    </header>

    <div class="content-wrapper">
        <!-- Left Section -->
        <section class="left-section">
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="card delays">
            <div class="text">
                <h3>DELAYS</h3>
                <div class="number">2</div>
                <div class="change">+8 Today</div>
            </div>
            <i class="fas fa-exclamation-triangle"></i>
            </div>

            <div class="card approved">
            <div class="text">
                <h3>APPROVED</h3>
                <div class="number">67</div>
                <div class="change">+4 Today</div>
            </div>
            <i class="fas fa-check-circle"></i>
            </div>

            <div class="card pending">
                <div class="text">
                <h3>PENDING</h3>
                <div class="number">5</div>
                <div class="change">+1 Today</div>
                </div>
                <i class="fas fa-clock"></i>
            </div>

            <div class="card rejects">
                <div class="text">
                <h3>REJECTS</h3>
                <div class="number">3</div>
                <div class="change">+2 Today</div>
                </div>
                <i class="fas fa-times-circle"></i>
            </div>
        </div>

        <!-- Pendings & Delays Table -->
        <div class="table-card">
            <div class="table-header">
            <h3><i class="fas fa-clock"></i> Pendings and Delays</h3>
            </div>

            <table>
            <thead>
                <tr>
                <th>Status</th>
                <th>Created By</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Price Predicted</th>
                <th>Currency</th>
                <th>Date</th>
                <th>Category</th>
                <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                <td><span class="pending-icon"><i class="fas fa-clock"></i></span></td>
                <td>Brad</td>
                <td>Microchips</td>
                <td>100</td>
                <td>7000</td>
                <td>USD</td>
                <td>11/21/2025</td>
                <td>Spare Parts</td>
                <td>Request for spare parts</td>
                </tr>
            </tbody>
            </table>
        </section>

        <!-- Recent Activity Sidebar -->
        <aside class="recent-activity">
        <h3><i class="fas fa-bell"></i> Recent Activity</h3>
        <div class="activity-item">
            <strong>New Request</strong><br>
            <small>by: Brad</small>
            <p>Waiting for Approval • 100 Microchips</p>
        </div>
        </aside>
    </div>
    </main>
</div>
</body>
</html>