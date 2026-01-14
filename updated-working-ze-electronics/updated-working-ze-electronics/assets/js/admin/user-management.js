let currentUserId = null;
let allUsers = []; // Store all users for searching

document.addEventListener("DOMContentLoaded", () => {
  console.log("JS LOADED - LOADING USERS...");
  loadUsers();

  // SEARCH FUNCTIONALITY
  const searchInput = document.querySelector(".search-input input");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.trim().toLowerCase();
      filterUsers(query);
    });
  }

  // ADD FORM SUBMIT
  document.getElementById("addForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const data = {
      firstname: document.getElementById("addFirstName").value.trim(),
      lastname: document.getElementById("addLastName").value.trim(),
      middlename: document.getElementById("addMiddleName").value.trim(),
      username: document.getElementById("addUsername").value.trim(),
      email: document.getElementById("addEmail").value.trim(),
      password: document.getElementById("addPassword").value,
      gender: document.getElementById("addGender").value,
      role: document.getElementById("addRole").value,
    };

    fetch("../PHP/add-user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((r) => r.text())
      .then((msg) => {
        alert(msg);
        if (msg.toLowerCase().includes("success")) {
          closeAddModal();
          this.reset();
          loadUsers();
        }
      })
      .catch((err) => alert("Add error: " + err));
  });

  // EDIT FORM SUBMIT
  document.getElementById("editForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const data = {
      id: currentUserId,
      firstname: document.getElementById("editFirstName").value.trim(),
      lastname: document.getElementById("editLastName").value.trim(),
      middlename: document.getElementById("editMiddleName").value.trim(),
      email: document.getElementById("editEmail").value.trim(),
      gender: document.getElementById("editGender").value,
      role: document.getElementById("editRole").value,
    };

    fetch("../PHP/update-user.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    })
      .then((r) => r.text())
      .then((msg) => {
        alert(msg);
        if (msg.toLowerCase().includes("success")) {
          closeEditModal();
          loadUsers();
        }
      })
      .catch((err) => alert("Edit error: " + err));
  });
});

function loadUsers() {
  fetch("../PHP/fetch-users.php")
    .then((r) => {
      if (!r.ok) throw new Error("HTTP " + r.status);
      return r.json();
    })
    .then((users) => {
      allUsers = users; // Save all users
      displayUsers(users); // Show all initially
    })
    .catch((err) => {
      document.getElementById(
        "employeeTableBody"
      ).innerHTML = `<tr><td colspan="8">Error loading users: ${err.message}</td></tr>`;
    });
}

// NEW: DISPLAY FILTERED OR ALL USERS
function displayUsers(users) {
  const tbody = document.getElementById("employeeTableBody");
  tbody.innerHTML = "";

  if (users.length === 0) {
    tbody.innerHTML =
      "<tr><td colspan='8' style='text-align:center; padding:30px; color:#aaa;'>No users found matching your search</td></tr>";
    return;
  }

  users.forEach((u) => {
    tbody.innerHTML += `
      <tr>
        <td>${u.id}</td>
        <td>${u.lastname || ""}</td>
        <td>${u.firstname || ""}</td>
        <td>${u.middlename || ""}</td>
        <td>${u.role}</td>
        <td>${u.gender || "—"}</td>
        <td>${u.email}</td>
        <td class="actions">
          <button class="edit-btn" onclick="openEditModal(${u.id}, '${(u.firstname || "").replace(/'/g, "\\'")}', '${(u.lastname || "").replace(/'/g, "\\'")}', '${(u.middlename || "").replace(/'/g, "\\'")}', '${u.email}', '${u.role}', '${u.gender || "Male"}')">
            <i class="fas fa-edit"></i>
          </button>
          <button class="delete-btn" onclick="deleteUser(${u.id})">
            <i class="fas fa-trash-alt"></i>
          </button>
        </td>
      </tr>`;
  });
}

// SEARCH FILTER FUNCTION
function filterUsers(query) {
  if (!query) {
    displayUsers(allUsers);
    return;
  }

  const filtered = allUsers.filter((user) => {
    const id = user.id.toString();
    const first = (user.firstname || "").toLowerCase();
    const last = (user.lastname || "").toLowerCase();
    const middle = (user.middlename || "").toLowerCase();
    const email = (user.email || "").toLowerCase();
    const role = (user.role || "").toLowerCase();
    const gender = (user.gender || "").toLowerCase();

    return (
      id.includes(query) ||
      first.includes(query) ||
      last.includes(query) ||
      middle.includes(query) ||
      email.includes(query) ||
      role.includes(query) ||
      gender.includes(query)
    );
  });

  displayUsers(filtered);
}

// MODAL FUNCTIONS
function openAddModal() {
  document.getElementById("addModal").style.display = "block";
}
function closeAddModal() {
  document.getElementById("addModal").style.display = "none";
}
function closeEditModal() {
  document.getElementById("editModal").style.display = "none";
}

function openEditModal(id, fn, ln, mn, email, role, gender) {
  currentUserId = id;
  document.getElementById("editFirstName").value = fn;
  document.getElementById("editLastName").value = ln;
  document.getElementById("editMiddleName").value = mn;
  document.getElementById("editEmail").value = email;
  document.getElementById("editRole").value = role;
  document.getElementById("editGender").value = gender;
  document.getElementById("editModal").style.display = "block";
}

function deleteUser(id) {
  if (confirm("Delete this user permanently?")) {
    fetch("../PHP/delete-user.php?id=" + id).then(() => loadUsers());
  }
}

window.onclick = (e) => {
  if (e.target.classList.contains("modal")) e.target.style.display = "none";
};
