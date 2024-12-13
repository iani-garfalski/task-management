<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Task Management</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Select2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Task Management</h2>
    <div class="d-flex justify-content-between mb-3">
      <button class="btn btn-primary" onclick="showTaskModal()">Add Task</button>
      <button class="btn btn-danger" onclick="logout()">Logout</button>
    </div>
    <input type="text" id="search" class="form-control mb-3" placeholder="Search tasks..." oninput="filterTasks()">

    <table class="table table-bordered">
      <thead>
        <tr>
          <th><input type="checkbox" id="selectAll" onclick="toggleSelectAll()"></th>
          <th>Title</th>
          <th>Description</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Due Date</th>
          <th>Category</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="task-table-body">
        <!-- Tasks will be populated here -->
      </tbody>
    </table>
  </div>

  <!-- Add/Edit Task Modal -->
  <div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Task</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Task Form -->
          <form id="taskForm">
            <!-- Fields for title, description, priority, etc. -->
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" onclick="saveTask()">Save</button>
        </div>
      </div>
    </div>
  </div>

  <script>
    function logout() {
      fetch('/api/auth/logout', {
        method: 'POST',
        headers: {
          Authorization: `Bearer ${localStorage.getItem('token')}`
        }
      }).then(() => {
        localStorage.removeItem('token');
        window.location.href = '/';
      });
    }

    async function fetchTasks() {
      alert('working')
      const token = localStorage.getItem('jwt_token');
      if (!token) {
        alert('Please log in.');
        window.location.href = '/login'; // Redirect to login page if no token
        return;
      }

      console.log('token', token);

      const response = await fetch('/api/tasks', {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        // Handle errors or redirects
        alert('Failed to fetch tasks or token expired.');
        window.location.href = '/login'; // Redirect to login if unauthorized
        return;
      }

      const data = await response.json();

      // Extract the tasks from the response's 'data' property
      const tasks = data.data; // The 'data' contains the array of tasks
      const taskContainer = document.getElementById('task-table-body');

      // Clear any existing tasks in the table
      taskContainer.innerHTML = '';

      // Loop through the tasks and append them to the table
      tasks.forEach(task => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${task.id}</td>
            <td>${task.title}</td>
            <td>${task.status}</td>
            <td>${task.priority}</td>
            <td>${task.due_date}</td>
            <td>
                <button class="btn btn-primary" onclick="editTask(${task.id})">Edit</button>
                <button class="btn btn-danger" onclick="deleteTask(${task.id})">Delete</button>
            </td>
        `;
        taskContainer.appendChild(row);
      });
    }

    document.addEventListener('DOMContentLoaded', fetchTasks);
  </script>
</body>

</html>