<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container mt-5">
    <h2 class="text-center">Login</h2>
    <form action="{{ route('login.post') }}" method="POST">
      @csrf
      <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
      </div>
      <button type="submit">Login</button>
    </form>
    <div class="mt-3">
      <a href="{{ route('register') }}">Don't have an account? Register here</a>
    </div>
  </div>

  <script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
      e.preventDefault();
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;

      const response = await fetch('/api/auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          email,
          password
        })
      });

      const data = await response.json();
      if (response.ok) {
        localStorage.setItem('token', data.access_token);
        window.location.href = '/app';
      } else {
        alert(data.error || 'Login failed');
      }
    });
  </script>
</body>

</html>

<script>
    document.querySelector('form').addEventListener('submit', async (e) => {
        e.preventDefault(); // Prevent default form submission

        const email = document.querySelector('#email').value;
        const password = document.querySelector('#password').value;

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password }),
            });

            if (!response.ok) {
                const error = await response.json();
                alert(error.error || 'Login failed. Please try again.');
                return;
            }

            const data = await response.json();
            localStorage.setItem('jwt_token', data.access_token); // Save token in localStorage
            alert('Login successful!');
            window.location.href = '/'; // Redirect to home page
        } catch (error) {
            console.error('Login error:', error);
            alert('An unexpected error occurred.');
        }
    });
</script>

