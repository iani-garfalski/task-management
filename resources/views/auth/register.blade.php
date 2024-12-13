<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Register</h2>
    <form action="{{ route('register.post') }}" method="POST">
      @csrf
      <div>
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
      </div>
      <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
      </div>
      <div>
        <label for="password_confirmation">Confirm Password:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" required>
      </div>
      <button type="submit">Register</button>
    </form>
    <div class="mt-3">
      <a href="{{ route('login') }}">Already have an account? Login here</a>
    </div>
  </div>

  <script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const password_confirmation = document.getElementById('password_confirmation').value;

      const response = await fetch('/api/auth/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation
        })
      });

      const data = await response.json();
      if (response.ok) {
        alert('Registration successful! Please login.');
        window.location.href = '/';
      } else {
        alert(data.error || 'Registration failed');
      }
    });
  </script>
</body>

</html>