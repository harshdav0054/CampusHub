document.addEventListener("DOMContentLoaded", function() {

    function showSection(id){
        document.querySelectorAll('.section').forEach(s => s.style.display='none');
        document.getElementById(id).style.display='block';
    }

    // --- FIXED SEARCH FUNCTION ---
    window.searchColleges = function() {
        const value = document.getElementById('collegeSearch').value.toLowerCase();
        document.querySelectorAll('#collegeTable tbody tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(value) ? '' : 'none';
        });
    }

    function filterStatus(status) {
        document.querySelectorAll('#activityTable tbody tr').forEach(row => {
            row.style.display = (status === 'all' || row.dataset.status === status) ? '' : 'none';
        });
        document.querySelectorAll('.status-filters button').forEach(b => b.classList.remove('active'));
        document.querySelector(`.status-filters button[onclick*="${status}"]`)?.classList.add('active');
    }

    function addCollegeRow(college) {
        const table = document.querySelector("#collegeTable tbody");
        const exists = Array.from(table.querySelectorAll("tr")).some(
            r => r.cells[0].textContent.toLowerCase() === college.college_name.toLowerCase()
        );
        if(exists) return;

        const newRow = document.createElement("tr");
        newRow.innerHTML = `
            <td>${college.college_name}</td>
            <td>${college.address}</td>
            <td>${college.email}</td>
            <td><a href="${college.website}" target="_blank">${college.website}</a></td>
        `;
        table.appendChild(newRow);
    }

    function updateDashboardCounts() {
        document.getElementById("collegeCount").textContent = document.querySelectorAll("#collegeTable tbody tr").length;
        document.getElementById("pendingCount").textContent = document.querySelectorAll("#activityTable tbody tr[data-status='pending']").length;
    }

    // Sidebar buttons
    document.querySelectorAll(".sidebar-btn").forEach(btn => {
        btn.addEventListener("click", function() {
            showSection(this.dataset.target);
        });
    });

    document.getElementById("logout-btn").addEventListener("click", function(e) {
    e.preventDefault(); // prevent default behavior

    Swal.fire({
        title: 'Are you sure?',
        text: "You will be logged out!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, logout!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'swal2-confirm btn-confirm',
            cancelButton: 'swal2-cancel btn-cancel'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '../homepage/homepage.php';
        }
    });
});

    // Approve/Reject
    document.getElementById("activityTable").addEventListener("click", function(e) {
        const btn = e.target.closest(".action-btn");
        if(!btn) return;

        const id = parseInt(btn.dataset.id);
        const action = btn.dataset.action;

        fetch(`update_approval.php?id=${id}&status=${action}`)
        .then(res => res.json())
        .then(data => {
            const row = document.getElementById(`approval_${id}`);
            if(action === "approved") {
                row.dataset.status = "approved";
                row.cells[2].innerHTML = `<span class="badge approved">Approved</span>`;
                row.cells[3].innerHTML = "";
                if(data.college) addCollegeRow(data.college);
            }
            if(action === "rejected") {
                row.remove();
            }
            updateDashboardCounts();
        })
        .catch(err => console.error(err));
    });

    showSection("dashboard");
    window.filterStatus = filterStatus;
});

