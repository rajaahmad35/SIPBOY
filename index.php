<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPBOY - Portfolio with Land Information</title>
    <!-- Using CDN for prototyping (not recommended for production) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.6s ease-out;
        }
        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }
        #login {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f0f0f0;
        }
        .auth-container {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            justify-content: center;
        }
        .login-form, .register-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        #home, #about, #map-section, #data-section, #admin-section, #contact, nav, footer {
            display: none;
        }
        #map {
            height: 600px;
            width: 100%;
        }
        .sidebar {
            position: absolute;
            top: 10px;
            left: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
            width: 300px;
        }
        .sidebar input {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar ul li {
            padding: 5px 0;
            cursor: pointer;
        }
        .sidebar ul li:hover {
            background: #f0f0f0;
        }
        .control-panel {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        .search-result {
            margin-top: 10px;
            color: #333;
        }
        #editModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }
        #editForm {
            background: white;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Login and Registration Page -->
    <div id="login">
        <div class="auth-container">
            <!-- Login Form -->
            <div class="login-form">
                <h2 class="text-2xl font-bold text-center mb-4">Login</h2>
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="username" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button onclick="login()" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">Login</button>
                </div>
            </div>
            <!-- Registration Form -->
            <div class="register-form">
                <h2 class="text-2xl font-bold text-center mb-4">Register</h2>
                <div class="space-y-4">
                    <div>
                        <label for="regUsername" class="block text-sm font-medium text-gray-700">Username</label>
                        <input type="text" id="regUsername" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="regPassword" class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" id="regPassword" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div>
                        <label for="regRole" class="block text-sm font-medium text-gray-700">Role</label>
                        <select id="regRole" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <button onclick="register()" class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">Register</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Navbar -->
    <nav class="bg-white shadow-lg fixed w-full z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-indigo-600">SIPBOY</span>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="#home" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Home</a>
                    <a href="#about" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">About</a>
                    <a href="#map-section" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Map</a>
                    <a href="#data-section" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Land Data</a>
                    <a href="#admin-section" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium" id="admin-link">Admin</a>
                    <a href="#contact" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Contact</a>
                    <button onclick="logout()" class="text-gray-700 hover:text-indigo-600 px-3 py-2 rounded-md text-sm font-medium">Logout</button>
                </div>
            </div>
        </div>
    </nav>
    <!-- Hero Section -->
    <section id="home" class="min-h-screen flex items-center justify-center bg-gradient-to-r from-indigo-500 to-purple-600 text-white">
        <div class="text-center fade-in">
            <h1 class="text-5xl font-bold mb-4">Welcome to SIPBOY</h1>
            <p class="text-xl mb-6">A platform for land information and management</p>
            <a href="#data-section" class="bg-white text-indigo-600 px-6 py-3 rounded-full font-semibold hover:bg-indigo-100 transition">View Land Data</a>
        </div>
    </section>
    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">About SIPBOY</h2>
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="md:w-1/2 fade-in">
                    <img src="https://via.placeholder.com/400" alt="Profile" class="rounded-lg shadow-lg">
                </div>
                <div class="md:w-1/2 fade-in">
                    <p class="text-gray-600 leading-relaxed">
                        SIPBOY is a web-based platform designed to provide land information for external users and manage land data for internal administrators. 
                        We integrate data from various sources to display land details such as price, NIB, status, and more, along with interactive maps.
                    </p>
                </div>
            </div>
        </div>
    </section>
    <!-- Map Section -->
    <section id="map-section" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">Interactive Map</h2>
            <div id="map-container">
                <div id="map" class="fade-in"></div>
                <div class="sidebar">
                    <input type="text" id="search" placeholder="Cari NIB...">
                    <button onclick="searchLocation()">Cari</button>
                    <ul id="menu">
                        <li onclick="toggleLayer('Bidang Tanah')">Bidang Tanah</li>
                        <li onclick="toggleLayer('Zona Nilai Tanah')">Zona Nilai Tanah</li>
                    </ul>
                    <div id="search-result" class="search-result"></div>
                </div>
                <div class="control-panel">
                    <!-- Tombol Hapus Semua Data Layer telah dihapus -->
                </div>
            </div>
        </div>
    </section>
    <!-- Land Data Section (User Eksternal) -->
    <section id="data-section" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">Land Data</h2>
            <div class="fade-in">
                <table id="land-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NIB</th>
                            <th>Harga Tanah</th>
                            <th>Status Tanah</th>
                            <th>Nama Pemilik</th>
                            <th>Luas</th>
                            <th>Penggunaan Lahan</th>
                            <th>Desa/Kelurahan</th>
                        </tr>
                    </thead>
                    <tbody id="land-data-body"></tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- Admin Section (User Internal) -->
    <section id="admin-section" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">Admin Dashboard</h2>
            <div class="fade-in">
                <h3 class="text-xl font-semibold mb-4">Manage Land Data</h3>
                <button onclick="refreshData()" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 mb-4">Refresh Data</button>
                <table id="admin-data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NIB</th>
                            <th>Harga Tanah</th>
                            <th>Status Tanah</th>
                            <th>Nama Pemilik</th>
                            <th>Luas</th>
                            <th>Penggunaan Lahan</th>
                            <th>Desa/Kelurahan</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="admin-data-body"></tbody>
                </table>
            </div>
        </div>
    </section>
    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8 fade-in">Contact Us</h2>
            <div class="max-w-lg mx-auto fade-in">
                <div class="bg-white p-6 rounded-lg shadow-lg">
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input type="text" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700">Message</label>
                            <textarea id="message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>
                        <button onclick="alert('Message sent!')" class="w-full bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700 transition">Send Message</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>© 2025 SIPBOY. All rights reserved.</p>
        </div>
    </footer>
    <!-- Edit Modal -->
    <div id="editModal" class="flex">
        <div id="editForm">
            <h3 class="text-xl font-bold mb-4">Edit Land Data</h3>
            <input type="hidden" id="editId">
            <div class="space-y-4">
                <div>
                    <label for="editNIB" class="block text-sm font-medium text-gray-700">NIB</label>
                    <input type="text" id="editNIB" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editHarga" class="block text-sm font-medium text-gray-700">Harga Tanah</label>
                    <input type="text" id="editHarga" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editStatus" class="block text-sm font-medium text-gray-700">Status Tanah</label>
                    <input type="text" id="editStatus" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editPemilik" class="block text-sm font-medium text-gray-700">Nama Pemilik</label>
                    <input type="text" id="editPemilik" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editLuas" class="block text-sm font-medium text-gray-700">Luas</label>
                    <input type="text" id="editLuas" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editPenggunaan" class="block text-sm font-medium text-gray-700">Penggunaan Lahan</label>
                    <input type="text" id="editPenggunaan" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editDesa" class="block text-sm font-medium text-gray-700">Desa/Kelurahan</label>
                    <input type="text" id="editDesa" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editLat" class="block text-sm font-medium text-gray-700">Latitude</label>
                    <input type="text" id="editLat" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="editLng" class="block text-sm font-medium text-gray-700">Longitude</label>
                    <input type="text" id="editLng" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <button onclick="saveEdit()" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">Simpan</button>
                <button onclick="closeModal()" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 ml-2">Batal</button>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        let map;
        let layers = {};
        let userRole = '';
        let editingId = null;
        // Simulated user database
        let users = [
            { username: 'admin', password: 'admin123', role: 'admin' },
            { username: 'user', password: 'user123', role: 'user' }
        ];
        // Simulated land data (replace with real Google Form data via API)
        let landData = [
            { id: 1, nib: "12345", harga: "Rp 500 jt", status: "Tersedia", pemilik: "Budi", luas: "200 m²", penggunaan: "Pertanian", desa: "Sungai", lat: -6.188858, lng: 106.814871 },
            { id: 2, nib: "67890", harga: "Rp 1 M", status: "Dijual", pemilik: "Ani", luas: "500 m²", penggunaan: "Perumahan", desa: "Jaringan", lat: -6.190000, lng: 106.815000 },
            { id: 3, nib: "11122", harga: "Rp 750 jt", status: "Tersedia", pemilik: "Cahyo", luas: "300 m²", penggunaan: "Komersial", desa: "Bunga", lat: -6.189000, lng: 106.813000 },
            { id: 4, nib: "22233", harga: "Rp 1.2 M", status: "Dijual", pemilik: "Dina", luas: "600 m²", penggunaan: "Industri", desa: "Pinang", lat: -6.191000, lng: 106.816000 },
            { id: 5, nib: "33344", harga: "Rp 400 jt", status: "Tersedia", pemilik: "Eko", luas: "150 m²", penggunaan: "Pertanian", desa: "Cemara", lat: -6.187000, lng: 106.812000 },
            { id: 6, nib: "44455", harga: "Rp 800 jt", status: "Dijual", pemilik: "Fani", luas: "400 m²", penggunaan: "Perumahan", desa: "Melati", lat: -6.192000, lng: 106.817000 },
            { id: 7, nib: "55566", harga: "Rp 1.5 M", status: "Tersedia", pemilik: "Gita", luas: "700 m²", penggunaan: "Komersial", desa: "Teratai", lat: -6.186000, lng: 106.811000 },
            { id: 8, nib: "66677", harga: "Rp 300 jt", status: "Dijual", pemilik: "Hadi", luas: "100 m²", penggunaan: "Pertanian", desa: "Mawar", lat: -6.193000, lng: 106.818000 },
            { id: 9, nib: "77788", harga: "Rp 900 jt", status: "Tersedia", pemilik: "Indah", luas: "450 m²", penggunaan: "Industri", desa: "Anggrek", lat: -6.185000, lng: 106.810000 },
            { id: 10, nib: "88899", harga: "Rp 1.1 M", status: "Dijual", pemilik: "Joko", luas: "550 m²", penggunaan: "Perumahan", desa: "Kamboja", lat: -6.194000, lng: 106.819000 },
            { id: 11, nib: "99900", harga: "Rp 600 jt", status: "Tersedia", pemilik: "Kiki", luas: "250 m²", penggunaan: "Komersial", desa: "Flamboyan", lat: -6.184000, lng: 106.809000 },
            { id: 12, nib: "10111", harga: "Rp 700 jt", status: "Dijual", pemilik: "Lina", luas: "350 m²", penggunaan: "Pertanian", desa: "Sakura", lat: -6.195000, lng: 106.820000 },
            { id: 13, nib: "11222", harga: "Rp 1.3 M", status: "Tersedia", pemilik: "Mira", luas: "650 m²", penggunaan: "Industri", desa: "Bougenville", lat: -6.183000, lng: 106.808000 },
            { id: 14, nib: "12333", harga: "Rp 450 jt", status: "Dijual", pemilik: "Nanda", luas: "200 m²", penggunaan: "Perumahan", desa: "Jasmine", lat: -6.196000, lng: 106.821000 },
            { id: 15, nib: "13444", harga: "Rp 850 jt", status: "Tersedia", pemilik: "Oki", luas: "400 m²", penggunaan: "Komersial", desa: "Lotus", lat: -6.182000, lng: 106.807000 },
            { id: 16, nib: "14555", harga: "Rp 1.4 M", status: "Dijual", pemilik: "Puji", luas: "600 m²", penggunaan: "Industri", desa: "Orchid", lat: -6.197000, lng: 106.822000 },
            { id: 17, nib: "15666", harga: "Rp 350 jt", status: "Tersedia", pemilik: "Rina", luas: "150 m²", penggunaan: "Pertanian", desa: "Rose", lat: -6.181000, lng: 106.806000 },
            { id: 18, nib: "16777", harga: "Rp 950 jt", status: "Dijual", pemilik: "Sari", luas: "500 m²", penggunaan: "Perumahan", desa: "Lily", lat: -6.198000, lng: 106.823000 },
            { id: 19, nib: "17888", harga: "Rp 1.6 M", status: "Tersedia", pemilik: "Tono", luas: "700 m²", penggunaan: "Komersial", desa: "Dahlia", lat: -6.180000, lng: 106.805000 },
            { id: 20, nib: "18999", harga: "Rp 550 jt", status: "Dijual", pemilik: "Umi", luas: "300 m²", penggunaan: "Industri", desa: "Tulip", lat: -6.199000, lng: 106.824000 }
        ];
        // Register function
        function register() {
            const username = document.getElementById('regUsername').value.trim();
            const password = document.getElementById('regPassword').value.trim();
            const role = document.getElementById('regRole').value;
            if (!username || !password) {
                alert('Username and password are required.');
                return;
            }
            if (users.find(user => user.username === username)) {
                alert('Username already exists.');
                return;
            }
            users.push({ username, password, role });
            alert('Registration successful! You can now log in.');
            document.getElementById('regUsername').value = '';
            document.getElementById('regPassword').value = '';
            document.getElementById('regRole').value = 'user';
        }
        // Login function
        function login() {
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const user = users.find(u => u.username === username && u.password === password);
            if (user) {
                userRole = user.role;
                document.getElementById('admin-link').style.display = userRole === 'admin' ? 'block' : 'none';
                document.getElementById('login').style.display = 'none';
                document.querySelector('nav').style.display = 'block';
                document.getElementById('home').style.display = 'flex';
                document.getElementById('about').style.display = 'block';
                document.getElementById('map-section').style.display = 'block';
                document.getElementById('map-container').style.display = 'block';
                document.querySelector('.sidebar').style.display = 'block';
                document.querySelector('.control-panel').style.display = 'block';
                document.getElementById('data-section').style.display = 'block';
                if (userRole === 'admin') {
                    document.getElementById('admin-section').style.display = 'block';
                }
                document.getElementById('contact').style.display = 'block';
                document.querySelector('footer').style.display = 'block';
                initializeMap();
                displayLandData();
                if (userRole === 'admin') {
                    displayAdminData();
                }
            } else {
                alert('Invalid username or password');
            }
        }
        // Logout function
        function logout() {
            userRole = '';
            document.getElementById('login').style.display = 'flex';
            document.querySelector('nav').style.display = 'none';
            document.getElementById('home').style.display = 'none';
            document.getElementById('about').style.display = 'none';
            document.getElementById('map-section').style.display = 'none';
            document.getElementById('map-container').style.display = 'none';
            document.querySelector('.sidebar').style.display = 'none';
            document.querySelector('.control-panel').style.display = 'none';
            document.getElementById('data-section').style.display = 'none';
            document.getElementById('admin-section').style.display = 'none';
            document.getElementById('contact').style.display = 'none';
            document.querySelector('footer').style.display = 'none';
            if (map) {
                map.remove();
                map = null;
            }
        }
        // Initialize the map
        function initializeMap() {
            map = L.map('map').setView([-6.188858, 106.814871], 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            layers = {
                'Bidang Tanah': L.geoJSON(null, { style: { color: 'blue', fillOpacity: 0.5 } }),
                'Zona Nilai Tanah': L.geoJSON(null, { style: { color: 'purple', fillOpacity: 0.5 } })
            };
            // Add markers for land data
            landData.forEach(data => {
                L.marker([data.lat, data.lng]).addTo(map)
                    .bindPopup(`NIB: ${data.nib}<br>Harga: ${data.harga}<br>Status: ${data.status}`)
                    .on('click', () => highlightLand(data.nib));
            });
            for (var layer in layers) {
                layers[layer].addTo(map);
            }
            L.control.scale().addTo(map);
            L.marker([-6.188858, 106.814871]).addTo(map)
                .bindPopup('Lokasi Kantor<br>Lat: -6.188858, Long: 106.814871')
                .openPopup();
        }
        // Toggle layers
        function toggleLayer(name) {
            if (map.hasLayer(layers[name])) {
                map.removeLayer(layers[name]);
            } else {
                layers[name].addTo(map);
            }
        }
        // Search function based on NIB
        function searchLocation() {
            const query = document.getElementById('search').value.trim();
            const resultDiv = document.getElementById('search-result');
            const matchedData = landData.find(data => data.nib === query);
            if (matchedData) {
                resultDiv.innerHTML = `Ditemukan: NIB ${matchedData.nib}, Pemilik: ${matchedData.pemilik}, Harga: ${matchedData.harga}`;
                map.setView([matchedData.lat, matchedData.lng], 15);
                highlightLand(matchedData.nib);
            } else {
                resultDiv.innerHTML = 'NIB tidak ditemukan.';
            }
        }
        // Highlight land based on NIB
        function highlightLand(nib) {
            const matchedData = landData.find(data => data.nib === nib);
            if (matchedData) {
                if (map.highlightedMarker) {
                    map.removeLayer(map.highlightedMarker);
                }
                map.highlightedMarker = L.marker([matchedData.lat, matchedData.lng], { icon: L.divIcon({ className: 'highlight-marker', html: '▲' }) }).addTo(map)
                    .bindPopup(`NIB: ${matchedData.nib}<br>Harga: ${matchedData.harga}<br>Status: ${matchedData.status}`)
                    .openPopup();
            }
        }
        // Display land data for external users
        function displayLandData() {
            const tbody = document.getElementById('land-data-body');
            tbody.innerHTML = '';
            landData.forEach(data => {
                const row = `
                    <tr>
                        <td>${data.id}</td>
                        <td>${data.nib}</td>
                        <td>${data.harga}</td>
                        <td>${data.status}</td>
                        <td>${data.pemilik}</td>
                        <td>${data.luas}</td>
                        <td>${data.penggunaan}</td>
                        <td>${data.desa}</td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
        // Display land data for admin
        function displayAdminData() {
            const tbody = document.getElementById('admin-data-body');
            tbody.innerHTML = '';
            landData.forEach(data => {
                const row = `
                    <tr>
                        <td>${data.id}</td>
                        <td>${data.nib}</td>
                        <td>${data.harga}</td>
                        <td>${data.status}</td>
                        <td>${data.pemilik}</td>
                        <td>${data.luas}</td>
                        <td>${data.penggunaan}</td>
                        <td>${data.desa}</td>
                        <td>
                            ${userRole === 'admin' ? `<button onclick="editData(${data.id})" class="text-blue-600 hover:underline">Edit</button>
                            <button onclick="deleteData(${data.id})" class="text-red-600 hover:underline ml-2">Delete</button>` : ''}
                        </td>
                    </tr>
                `;
                tbody.innerHTML += row;
            });
        }
        // Simulate refresh data
        function refreshData() {
            alert('Data refreshed! (Simulated)');
            displayAdminData();
        }
        // Edit data
        function editData(id) {
            if (userRole !== 'admin') return;
            editingId = id;
            const data = landData.find(d => d.id === id);
            document.getElementById('editId').value = data.id;
            document.getElementById('editNIB').value = data.nib;
            document.getElementById('editHarga').value = data.harga;
            document.getElementById('editStatus').value = data.status;
            document.getElementById('editPemilik').value = data.pemilik;
            document.getElementById('editLuas').value = data.luas;
            document.getElementById('editPenggunaan').value = data.penggunaan;
            document.getElementById('editDesa').value = data.desa;
            document.getElementById('editLat').value = data.lat;
            document.getElementById('editLng').value = data.lng;
            document.getElementById('editModal').style.display = 'flex';
        }
        // Save edited data
        function saveEdit() {
            if (userRole !== 'admin') return;
            const id = parseInt(document.getElementById('editId').value);
            const data = landData.find(d => d.id === id);
            if (data) {
                data.nib = document.getElementById('editNIB').value;
                data.harga = document.getElementById('editHarga').value;
                data.status = document.getElementById('editStatus').value;
                data.pemilik = document.getElementById('editPemilik').value;
                data.luas = document.getElementById('editLuas').value;
                data.penggunaan = document.getElementById('editPenggunaan').value;
                data.desa = document.getElementById('editDesa').value;
                data.lat = parseFloat(document.getElementById('editLat').value) || data.lat;
                data.lng = parseFloat(document.getElementById('editLng').value) || data.lng;
                // Update map markers
                if (map) {
                    map.eachLayer(layer => {
                        if (layer instanceof L.Marker && layer.options.icon.options.className !== 'highlight-marker') {
                            map.removeLayer(layer);
                        }
                    });
                    landData.forEach(d => {
                        L.marker([d.lat, d.lng]).addTo(map)
                            .bindPopup(`NIB: ${d.nib}<br>Harga: ${d.harga}<br>Status: ${d.status}`)
                            .on('click', () => highlightLand(d.nib));
                    });
                }
                displayAdminData();
                displayLandData();
                closeModal();
            }
        }
        // Close modal
        function closeModal() {
            document.getElementById('editModal').style.display = 'none';
            editingId = null;
        }
        // Delete data
        function deleteData(id) {
            if (userRole !== 'admin') return;
            landData = landData.filter(data => data.id !== id);
            displayAdminData();
            displayLandData();
            if (map) {
                map.eachLayer(layer => {
                    if (layer instanceof L.Marker && layer.options.icon.options.className !== 'highlight-marker') {
                        map.removeLayer(layer);
                    }
                });
                landData.forEach(data => {
                    L.marker([data.lat, d.lng]).addTo(map)
                        .bindPopup(`NIB: ${data.nib}<br>Harga: ${data.harga}<br>Status: ${data.status}`)
                        .on('click', () => highlightLand(data.nib));
                });
            }
        }
        // Smooth scrolling for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        // Fade-in animation on scroll
        const fadeIns = document.querySelectorAll('.fade-in');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        fadeIns.forEach(element => observer.observe(element));
    </script>
    <style>
        .highlight-marker {
            background-color: yellow;
            border: 2px solid red;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            color: black;
            font-weight: bold;
        }
    </style>
</body>
</html>
