<!DOCTYPE html><html><head><script src="https://cdn.tailwindcss.com"></script><title>Register</title></head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen">
    <div class="bg-white p-8 rounded-xl shadow-sm border w-96">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">Register</h2>
        <form action="/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="text" name="name" placeholder="Name" required class="w-full mb-4 px-4 py-2 border rounded-lg">
            <input type="email" name="email" placeholder="Email" required class="w-full mb-4 px-4 py-2 border rounded-lg">
            <input type="password" name="password" placeholder="Password" required class="w-full mb-6 px-4 py-2 border rounded-lg">
            <button type="submit" class="w-full bg-indigo-600 text-white py-2 rounded-lg font-medium">Register</button>
        </form>
        <p class="mt-4 text-sm text-center">Already have an account? <a href="/login" class="text-slate-600">Login</a></p>
    </div>
</body></html>


