@extends('layouts.app')

@section('title', 'Manage Categories')

@section('content')

<h2>Manage Categories</h2>
<div class="mb-4">
  <h4>Add a New Category</h4>
  <div class="d-flex">
    <input type="text" class="form-control me-2" id="newCategoryInput" placeholder="New Category Name">
    <button class="btn btn-primary" onclick="addNewCategory()">Add Category</button>
  </div>
  <small class="text-muted">Add new categories to use in tasks.</small>
</div>

<div>
  <h4>Existing Categories</h4>
  <ul id="categoriesList" class="list-group">
    <!-- Categories will be dynamically added here -->
  </ul>
</div>

<script>
  let categories = [];

  // Fetch categories from the server
  async function fetchCategories() {
    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    try {
      const response = await fetch('/api/categories', {
        headers: {
          'Authorization': `Bearer ${token}`
        },
      });

      if (!response.ok) {
        throw new Error('Failed to fetch categories.');
      }

      const data = await response.json();
      console.log('Fetched categories:', data);

      // Ensure categories is an array
      categories = Array.isArray(data) ? data : data.data || [];
      renderCategories();
    } catch (error) {
      console.error('Error fetching categories:', error);
      alert('Could not load categories.');
    }
  }


  // Render categories in the list
  function renderCategories() {
    const list = document.getElementById('categoriesList');
    if (!Array.isArray(categories)) {
      console.error('Categories is not an array:', categories);
      categories = [];
    }

    list.innerHTML = categories
      .map(category => `<li class="list-group-item">${category.name}</li>`)
      .join('');
  }


  // Add a new category
  async function addNewCategory() {
    const categoryInput = document.getElementById('newCategoryInput');
    const newCategory = categoryInput.value.trim();

    if (!newCategory) {
      alert('Category name cannot be empty.');
      return;
    }

    const token = localStorage.getItem('jwt_token');
    if (!token) {
      alert('Please log in.');
      window.location.href = '/login';
      return;
    }

    try {
      const response = await fetch('/api/categories', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify({
          name: newCategory
        }),
      });

      if (!response.ok) {
        throw new Error('Failed to add category.');
      }

      alert('Category added successfully!');
      categoryInput.value = '';
      fetchCategories();
    } catch (error) {
      console.error('Error adding category:', error);
      alert('An error occurred while adding the category.');
    }
  }

  document.addEventListener('DOMContentLoaded', fetchCategories);
</script>
@endsection