@extends('layouts.app')

@section('title', 'Home')

@section('content')

<!-- Task Table -->
<div>
  <h2>Your Tasks</h2>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Description</th>
        <th>Status</th>
        <th>Priority</th>
        <th>Due Date</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="task-table-body">
      <!-- Tasks will be dynamically injected here -->
    </tbody>
    <tfoot>
    </tfoot>
  </table>
  <!-- Pagination controls -->
  <div id="pagination-controls" class="d-flex justify-content-between mt-3">
    <button id="prev-page" class="btn btn-secondary btn-sm" onclick="changePage('prev')">← Previous</button>
    <div id="page-numbers" class="d-flex align-items-center"></div>
    <button id="next-page" class="btn btn-secondary btn-sm" onclick="changePage('next')">Next →</button>
  </div>
</div>

<script>
  let saveTimeout;

  async function saveNewTask() {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    const title = document.getElementById('newTaskTitle').value;
    const description = document.getElementById('newTaskDescription').value;
    const status = document.getElementById('newTaskStatus').value;
    const priority = document.getElementById('newTaskPriority').value;
    const dueDate = document.getElementById('newTaskDueDate').value;

    if (!title.trim()) {
      alert('Title is required.');
      return;
    }

    const newTask = {
      title,
      description,
      status,
      priority,
      due_date: dueDate,
    };

    try {
      const response = await fetch('/api/tasks', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`,
        },
        body: JSON.stringify(newTask),
      });

      if (!response.ok) {
        const error = await response.json();
        alert(`Failed to save task: ${error.message || 'Unknown error'}`);
        return;
      }

      alert('Task saved successfully!');
      fetchTasks(); // Refresh the task list
    } catch (error) {
      console.error('Error:', error);
      alert('Failed to save the task. Please try again.');
    }
  }

  let currentPage = 1;
  let totalPages = 1;

  async function fetchTasks(page = 1) {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    try {
      const response = await fetch(`/api/tasks?page=${page}`, {
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      });

      if (!response.ok) {
        alert('Failed to fetch tasks or token expired.');
        window.location.href = '/login';
        return;
      }

      const data = await response.json();
      const tasks = data.data;
      totalPages = data.meta.last_page;

      const taskContainer = document.getElementById('task-table-body');
      taskContainer.innerHTML = '';

      // Add the new task row at the top
      const newTaskRow = generateTaskRow(null, true);
      taskContainer.appendChild(newTaskRow);

      // Generate rows for tasks
      tasks.forEach((task, index) => {
        task.index = (page - 1) * 10 + index + 1; // Compute row number
        const row = generateTaskRow(task);
        taskContainer.appendChild(row);
      });

      updatePagination(page);
    } catch (error) {
      console.error('Error fetching tasks:', error);
      alert('An error occurred while fetching tasks.');
    }
  }

  function handleStatusChange(selectElement) {
    const currentStatus = selectElement.getAttribute('data-current-status'); // Get the current status
    const newStatus = selectElement.value;
    const row = selectElement.closest('tr');
    const priorityField = row.querySelector('[data-field="priority"]');
    const dueDateField = row.querySelector('[data-field="due_date"]');
    const titleField = row.querySelector('[data-field="title"]');
    const descriptionField = row.querySelector('[data-field="description"]');

    if (currentStatus === 'Completed' && newStatus !== 'In Progress') {
      // Revert the change if it's not to "In Progress"
      alert('You can only change status from Completed to In Progress.');
      selectElement.value = currentStatus; // Revert to the previous value
      return;
    }

    // Update the editable state of other fields
    if (newStatus === 'Completed') {
      // Disable editing on the row when status is "Completed"
      priorityField.disabled = true;
      dueDateField.disabled = true;
      titleField.contentEditable = false;
      descriptionField.contentEditable = false;
    } else {
      // Enable editing when status is not "Completed"
      priorityField.disabled = false;
      dueDateField.disabled = false;
      titleField.contentEditable = true;
      descriptionField.contentEditable = true;
    }

    // Update the current status for future checks
    selectElement.setAttribute('data-current-status', newStatus);

    // Optionally call updateTask to save the new status in the backend
    const taskId = selectElement.getAttribute('data-task-id');
    updateTask(taskId, 'status', newStatus);
  }

  // Handle the updating of a task when editing a field
  document.addEventListener('input', (event) => {
    if (event.target && event.target.classList.contains('editable')) {
      const taskId = event.target.getAttribute('data-task-id');
      const field = event.target.getAttribute('data-field');
      const newValue = event.target.value || event.target.innerText;

      updateTask(taskId, field, newValue);
    }
  });

  // Update task in the database
  async function updateTask(taskId, field, newValue) {
    const updatedData = {};
    updatedData[field] = newValue;

    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    clearTimeout(saveTimeout);
    saveTimeout = setTimeout(async () => {
      try {
        const response = await fetch(`/api/tasks/${taskId}`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`,
          },
          body: JSON.stringify(updatedData),
        });

        if (!response.ok) {
          throw new Error('Failed to update task.');
        }
        alert('Task updated successfully!');
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the task.');
      }
    }, 1000);
  }

  // Function to delete a task
  function deleteTask(taskId) {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    fetch(`/api/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
        },
      })
      .then(response => {
        if (!response.ok) {
          throw new Error('Failed to delete task.');
        }
        alert('Task deleted successfully!');
        fetchTasks(currentPage);
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the task.');
      });
  }

  // Update pagination buttons and page numbers
  function updatePagination(page) {
    currentPage = page;
    document.getElementById('prev-page').disabled = currentPage === 1;
    document.getElementById('next-page').disabled = currentPage === totalPages;

    const pageNumbersContainer = document.getElementById('page-numbers');
    pageNumbersContainer.innerHTML = '';

    for (let i = 1; i <= totalPages; i++) {
      const pageButton = document.createElement('button');
      pageButton.classList.add('btn', 'btn-sm', 'btn-outline-primary', 'mx-1');
      pageButton.textContent = i;
      pageButton.onclick = () => fetchTasks(i);
      if (i === currentPage) {
        pageButton.classList.add('active');
      }
      pageNumbersContainer.appendChild(pageButton);
    }
  }

  // Navigate between pages
  function changePage(direction) {
    if (direction === 'prev' && currentPage > 1) {
      fetchTasks(currentPage - 1);
    } else if (direction === 'next' && currentPage < totalPages) {
      fetchTasks(currentPage + 1);
    }
  }


  function generateTaskRow(task = null, isNew = false) {
    const row = document.createElement('tr');

    if (isNew) {
      row.setAttribute('id', 'newTaskRow');
      row.classList.add('fw-bold', 'border', 'border-danger', 'p-5');

      row.innerHTML = `
      <td class="text-center align-middle">New</td>
      <td><input type="text" class="form-control" id="newTaskTitle" placeholder="Task Title"></td>
      <td><input type="text" class="form-control" id="newTaskDescription" placeholder="Description"></td>
      <td>
        <select class="form-control" id="newTaskStatus">
          <option value="Pending">Pending</option>
          <option value="In Progress">In Progress</option>
          <option value="Completed">Completed</option>
        </select>
      </td>
      <td>
        <select class="form-control" id="newTaskPriority">
          <option value="Low">Low</option>
          <option value="Medium">Medium</option>
          <option value="High">High</option>
        </select>
      </td>
      <td><input type="date" class="form-control" id="newTaskDueDate"></td>
      <td>
        <button class="btn btn-success btn-sm" onclick="saveNewTask()">Save</button>
      </td>
    `;
    } else {
      row.innerHTML = `
      <td>${task.index}</td>
      <td contenteditable="${task.status === 'Completed' ? 'false' : 'true'}" 
          class="editable" 
          data-field="title" 
          data-task-id="${task.id}">${task.title}</td>
      <td contenteditable="${task.status === 'Completed' ? 'false' : 'true'}" 
          class="editable" 
          data-field="description" 
          data-task-id="${task.id}">${task.description}</td>
      <td>
        <select class="form-control editable" data-field="status" 
                data-task-id="${task.id}" 
                data-current-status="${task.status}" 
                onchange="handleStatusChange(this)">
          <option value="Pending" ${task.status === 'Pending' ? 'selected' : ''}>Pending</option>
          <option value="In Progress" ${task.status === 'In Progress' ? 'selected' : ''}>In Progress</option>
          <option value="Completed" ${task.status === 'Completed' ? 'selected' : ''}>Completed</option>
        </select>
      </td>
      <td>
        <select class="form-control editable" data-field="priority" 
                data-task-id="${task.id}" 
                ${task.status === 'Completed' ? 'disabled' : ''}>
          <option value="Low" ${task.priority === 'Low' ? 'selected' : ''}>Low</option>
          <option value="Medium" ${task.priority === 'Medium' ? 'selected' : ''}>Medium</option>
          <option value="High" ${task.priority === 'High' ? 'selected' : ''}>High</option>
        </select>
      </td>
      <td>
        <input type="date" class="form-control editable" 
               data-field="due_date" 
               data-task-id="${task.id}" 
               value="${task.due_date}" 
               ${task.status === 'Completed' ? 'disabled' : ''}>
      </td>
      <td>
        <button class="btn btn-danger btn-sm" onclick="deleteTask(${task.id})">Delete</button>
      </td>
    `;
    }

    return row;
  }

  // On DOM ready initialize tasks
  document.addEventListener('DOMContentLoaded', async () => {
    fetchTasks(currentPage); // Fetch tasks
  });
</script>
@endsection