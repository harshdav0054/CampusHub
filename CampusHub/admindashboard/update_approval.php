<?php
include '../db.php';
header('Content-Type: application/json');

if(isset($_GET['id'], $_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = $_GET['status'];

    if(in_array($status,['approved','rejected'])) {
        mysqli_query($conn,"UPDATE approvals SET status='$status' WHERE id=$id");

        if($status==='approved'){
            // Get college info
            $res = mysqli_query($conn,"SELECT cp.id, cp.college_name, cp.address, ca.email, cp.website
                                      FROM approvals a
                                      JOIN college_profiles cp ON a.college_id = cp.id
                                      JOIN college_accounts ca ON cp.college_account_id = ca.id
                                      WHERE a.id=$id");
            $college = mysqli_fetch_assoc($res);

            echo json_encode(['success'=>true,'college'=>$college]);
            exit;
        }
    }
}
echo json_encode(['success'=>true]);
?>
