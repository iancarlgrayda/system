<?php
session_start();

// 🔒 Restrict access: only logged-in staff allowed
if (!isset($_SESSION["username"]) || $_SESSION["role"] !== "staff") {
  header("Location: ../login.html");
  exit();
}

$username = $_SESSION["username"];
$role = $_SESSION["role"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Staff Dashboard | S&D CRE8TIVE</title>
  <link rel="stylesheet" href="stafffp.css" />
  <link rel="icon" href="../logo.png" type="image/png" />
</head>
<body>
  <header class="header">
    <div class="logo-container">
      <img src="../logo.png" alt="S&D CRE8TIVE Logo" class="logo" />
      <h1>S&D CRE8TIVE</h1>
    </div>
    <div class="header-actions">
      <button id="createEventBtn" class="create-btn">+ Create Event</button>
      <a href="../login.html" class="logout-btn">Logout</a>
    </div>
  </header>

  <section class="stats-container">
    <div class="stat-card">
      <h3>Total Events</h3>
      <p class="stat-value" id="totalEvents">0</p>
    </div>
    <div class="stat-card">
      <h3>Upcoming</h3>
      <p class="stat-value" id="upcomingCount">0</p>
    </div>
    <div class="stat-card">
      <h3>Total Guests</h3>
      <p class="stat-value" id="totalAttendees">0</p>
    </div>
    <div class="stat-card">
      <h3>Avg. Attendance</h3>
      <p class="stat-value" id="avgAttendance">0%</p>
    </div>
  </section>

  <section class="filter-bar">
    <input type="text" id="searchInput" placeholder="🔍 Search events..." />
    <select id="statusFilter">
      <option value="all">All Status</option>
      <option value="upcoming">Upcoming</option>
      <option value="ongoing">Ongoing</option>
      <option value="completed">Completed</option>
    </select>
    <select id="categoryFilter">
      <option value="all">All Categories</option>
      <option value="birthday">Birthday</option>
      <option value="wedding">Wedding</option>
      <option value="christening">Christening</option>
      <option value="party">Other Parties</option>
    </select>
  </section>

  <main id="eventGrid" class="event-grid"></main>

  <!-- Event Modal -->
  <div id="eventModal" class="modal">
    <div class="modal-content">
      <h2 id="modalTitle">Create Event</h2>
      <form id="eventForm">
        <input type="hidden" id="editIndex" />

        <label>Event Name</label>
        <input type="text" id="eventName" required />

        <label>Category</label>
        <select id="eventCategory">
          <option value="birthday">Birthday</option>
          <option value="wedding">Wedding</option>
          <option value="christening">Christening</option>
          <option value="party">Other Parties</option>
        </select>

        <label>Status</label>
        <select id="eventStatus">
          <option value="upcoming">Upcoming</option>
          <option value="ongoing">Ongoing</option>
          <option value="completed">Completed</option>
        </select>

        <label>Date</label>
        <input type="date" id="eventDate" required />

        <label>Time</label>
        <input type="time" id="eventTime" required />

        <label>Location</label>
        <input type="text" id="eventLocation" required />

        <label>Attendees (current / max)</label>
        <input type="text" id="eventAttendees" placeholder="e.g. 25 / 100" required />

        <div class="modal-actions">
          <button type="submit" class="save-btn" id="saveEventBtn">Save</button>
          <button type="button" class="cancel-btn" id="cancelModal">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="footer">
    <p>© <?php echo date("Y"); ?> S&D CRE8TIVE. All rights reserved.</p>
  </footer>

  <script>
    const eventGrid = document.getElementById("eventGrid");
    const modal = document.getElementById("eventModal");
    const cancelModal = document.getElementById("cancelModal");
    const eventForm = document.getElementById("eventForm");
    const modalTitle = document.getElementById("modalTitle");
    const saveEventBtn = document.getElementById("saveEventBtn");
    const editIndex = document.getElementById("editIndex");

    let events = [
      { name: "Sofia’s 18th Birthday", category: "birthday", status: "upcoming", date: "2025-11-20", time: "18:00", location: "Blue Lagoon Resort", attendees: "80 / 100" },
      { name: "John & May Wedding", category: "wedding", status: "ongoing", date: "2025-11-02", time: "10:00", location: "St. Peter Church", attendees: "150 / 200" },
      { name: "Lucas Christening", category: "christening", status: "completed", date: "2025-10-10", time: "09:00", location: "Holy Family Parish", attendees: "45 / 50" }
    ];

    function updateStats() {
      document.getElementById("totalEvents").textContent = events.length;
      const upcoming = events.filter(e => e.status === "upcoming").length;
      document.getElementById("upcomingCount").textContent = upcoming;

      let total = 0, max = 0;
      events.forEach(e => {
        const parts = e.attendees.split("/").map(x => parseInt(x.trim()) || 0);
        total += parts[0]; max += parts[1];
      });
      document.getElementById("totalAttendees").textContent = total;
      document.getElementById("avgAttendance").textContent = max > 0 ? Math.round((total / max) * 100) + "%" : "0%";
    }

    function renderEvents() {
      eventGrid.innerHTML = "";
      events.forEach((e, i) => {
        const card = document.createElement("div");
        card.className = `event-card ${e.status}`;
        card.innerHTML = `
          <div class="event-header">
            <h3>${e.name}</h3>
            <span class="event-status">${e.status}</span>
          </div>
          <p>📅 ${e.date} ${e.time}</p>
          <p>📍 ${e.location}</p>
          <p>👥 ${e.attendees}</p>
          <p>🎉 ${e.category.charAt(0).toUpperCase() + e.category.slice(1)}</p>
          <div class="event-actions">
            <button class="edit-btn" onclick="editEvent(${i})">Edit</button>
            <button class="delete-btn" onclick="deleteEvent(${i})">Delete</button>
          </div>`;
        eventGrid.appendChild(card);
      });
      updateStats();
    }

    document.getElementById("createEventBtn").onclick = () => {
      modal.style.display = "flex";
      modalTitle.textContent = "Create Event";
      eventForm.reset();
      editIndex.value = "";
    };
    cancelModal.onclick = () => modal.style.display = "none";

    eventForm.onsubmit = (e) => {
      e.preventDefault();
      const data = {
        name: eventForm.eventName.value,
        category: eventForm.eventCategory.value,
        status: eventForm.eventStatus.value,
        date: eventForm.eventDate.value,
        time: eventForm.eventTime.value,
        location: eventForm.eventLocation.value,
        attendees: eventForm.eventAttendees.value
      };
      if (editIndex.value) events[editIndex.value] = data;
      else events.push(data);
      modal.style.display = "none";
      renderEvents();
    };

    function editEvent(i) {
      const e = events[i];
      modal.style.display = "flex";
      modalTitle.textContent = "Edit Event";
      eventForm.eventName.value = e.name;
      eventForm.eventCategory.value = e.category;
      eventForm.eventStatus.value = e.status;
      eventForm.eventDate.value = e.date;
      eventForm.eventTime.value = e.time;
      eventForm.eventLocation.value = e.location;
      eventForm.eventAttendees.value = e.attendees;
      editIndex.value = i;
    }

    function deleteEvent(i) {
      if (confirm("Delete this event?")) {
        events.splice(i, 1);
        renderEvents();
      }
    }

    renderEvents();
  </script>
</body>
</html>

