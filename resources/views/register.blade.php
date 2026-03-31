<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Page</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .container { background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); width: 300px; }
    h2 { text-align: center; margin-bottom: 20px; }
    input[type="text"], input[type="email"], input[type="password"] { width: 100%; padding: 10px; margin: 8px 0; border: 1px solid #ccc; border-radius: 5px; }
    button { width: 100%; padding: 10px; background: #28a745; border: none; color: #fff; font-size: 16px; border-radius: 5px; cursor: pointer; }
    button:hover { background: #1e7e34; }
    .footer { text-align: center; margin-top: 10px; font-size: 14px; }
    .footer a { color: #007bff; text-decoration: none; }
    .error { color: red; font-size: 14px; margin: 5px 0; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Register</h2>

    <!-- ✅ Show Validation Errors -->
    @if ($errors->any())
      <div class="error">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
      <input type="text" name="name" placeholder="Username" value="{{ old('name') }}" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
      <button type="submit">Register</button>
    </form>

    <div class="footer">
      <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </div>
  </div>
</body>
</html>
