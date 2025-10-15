<?php
session_start();
include '../db.php';
?>

<!DOCTYPE html>
<html lang="en">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>CampusHub Admin Dashboard</title>
  <link rel="icon" type="image/png" href="favicon.png">
  <link rel="stylesheet" href="admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>
  <body>
    <div class="sidebar">
      <h2>CampusHub Admin</h2>
      <ul>
        <li class="sidebar-btn" data-target="dashboard"><i class="fa-solid fa-chart-line"></i> Dashboard</li>
        <li class="sidebar-btn" data-target="colleges"><i class="fa-solid fa-building-columns"></i> Manage Colleges</li>
        <li class="sidebar-btn" data-target="approval"><i class="fa-solid fa-file-circle-check"></i> Approval</li>
        <li id="logout-btn" class="logout-bottom"><i class="fa-solid fa-right-from-bracket"></i> Logout</li>
      </ul>
    </div>

    <div class="main-content">
      <header>
        <h1 id="page-title">Welcome, Admin</h1>
      </header>

      <!-- Dashboard -->
      <section id="dashboard" class="section">
        <div class="cards">
          <div class="card">
            <i class="fa-solid fa-building-columns"></i><br>
            Colleges<br>
            <strong id="collegeCount">
              <?php
              $res = mysqli_query($conn, "SELECT COUNT(*) as total 
                                          FROM approvals WHERE status='approved'");
              $row = mysqli_fetch_assoc($res);
              echo $row['total'];
              ?>
            </strong>
          </div>
          <div class="card">
            <i class="fa-solid fa-clock"></i><br>
            Pending Approvals<br>
            <strong id="pendingCount">
              <?php
              $res2 = mysqli_query($conn, "SELECT COUNT(*) as pending FROM approvals WHERE status='pending'");
              $row2 = mysqli_fetch_assoc($res2);
              echo $row2['pending'];
              ?>
            </strong>
          </div>
        </div>
      </section>

      <!-- Manage Colleges -->
      <section id="colleges" class="section" style="display:none;">
        <h2>Manage Colleges</h2>
        <div class="search-bar">
          <i class="fa-solid fa-magnifying-glass"></i>
          <input type="text" id="collegeSearch" placeholder="Search colleges..." onkeyup="searchColleges()"/>
        </div>
        <table id="collegeTable">
          <thead>
            <tr>
              <th>Name</th><th>Location</th><th>Email</th><th>Website</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $colleges = mysqli_query($conn, "SELECT cp.*, ca.email 
                                            FROM approvals a
                                            JOIN college_profiles cp ON a.college_id = cp.id
                                            JOIN college_accounts ca ON cp.college_account_id = ca.id
                                            WHERE a.status='approved'");
            while ($college = mysqli_fetch_assoc($colleges)):
                $website = $college['website'] ?: "#";
            ?>
            <tr>
              <td><?= $college['college_name'] ?></td>
              <td><?= $college['address'] ?></td>
              <td><?= $college['email'] ?></td>
              <td><a href="<?= $website ?>" target="_blank"><?= $website ?></a></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>

      <!-- Approval -->
      <section id="approval" class="section" style="display:none;">
        <h2>Approval Requests</h2>
        <div class="status-filters">
          <button onclick="filterStatus('all')" class="active"><i class="fa-solid fa-list"></i> All</button>
          <button onclick="filterStatus('approved')"><i class="fa-solid fa-circle-check"></i> Approved</button>
          <button onclick="filterStatus('rejected')"><i class="fa-solid fa-circle-xmark"></i> Rejected</button>
          <button onclick="filterStatus('pending')"><i class="fa-solid fa-clock"></i> Pending</button>
        </div>
        <table id="activityTable">
          <thead>
            <tr>
              <th>Name</th><th>Date</th><th>Status</th><th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $approvals = mysqli_query($conn, "SELECT a.id as approval_id, cp.college_name, a.status, a.date
                                              FROM approvals a
                                              JOIN college_profiles cp ON a.college_id = cp.id
                                              ORDER BY a.date DESC");
            while ($app = mysqli_fetch_assoc($approvals)):
                $status = $app['status'];
            ?>
            <tr data-status="<?= $status ?>" id="approval_<?= $app['approval_id'] ?>">
                <td><?= $app['college_name'] ?></td>
                <td><?= $app['date'] ?></td>
                <td><span class="badge <?= $status ?>"><?= ucfirst($status) ?></span></td>
                <td>
                    <?php if ($status === 'pending'): ?>
                        <button class="action-btn approve-btn" data-id="<?= $app['approval_id'] ?>" data-action="approved">
                            <i class="fa-solid fa-check"></i> Approve
                        </button>
                        <button class="action-btn reject-btn" data-id="<?= $app['approval_id'] ?>" data-action="rejected">
                            <i class="fa-solid fa-xmark"></i> Reject
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </section>

    </div>

    <script src="admin.js"></script>
  </body>
</html>
