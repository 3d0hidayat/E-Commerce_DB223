<?php include 'db.php'; session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Figureku</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
  <script type="module" src="https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body { font-family: 'Montserrat', 'Poppins', sans-serif; }
    .section { transition: all 0.5s ease; }
    .glass {
      background: rgba(30, 27, 75, 0.7);
      box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
      backdrop-filter: blur(8px);
      -webkit-backdrop-filter: blur(8px);
      border-radius: 16px;
      border: 1px solid rgba(255,255,255,0.12);
    }
    .btn-main {
      background: linear-gradient(90deg, #7c3aed 0%, #e879f9 100%);
      color: #fff;
      font-weight: bold;
      padding: 0.5rem 1.5rem;
      border-radius: 9999px;
      box-shadow: 0 4px 16px 0 rgba(124,58,237,0.15);
      transition: background 0.3s, transform 0.2s;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-main:hover {
      background: linear-gradient(90deg, #e879f9 0%, #7c3aed 100%);
      transform: translateY(-2px) scale(1.04);
    }
    .aspect-square {
      aspect-ratio: 1/1;
    }
    .product-card {
      animation: fadeInUp 0.7s cubic-bezier(.39,.575,.565,1) both;
    }
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(40px); }
      100% { opacity: 1; transform: none; }
    }
  </style>
</head>
<body class="bg-gray-900 text-white">

  <!-- Notifikasi Status Pesanan (Home User) - Popup -->
  <style>
    .notif-popup {
      position: fixed;
      top: 32px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 50;
      min-width: 320px;
      max-width: 90vw;
      box-shadow: 0 8px 32px 0 rgba(31,38,135,0.18);
      border-width: 2px;
      border-style: solid;
      font-size: 1.08rem;
      font-family: 'Montserrat', 'Poppins', sans-serif;
      letter-spacing: 0.01em;
      animation: fadeInNotif 0.7s cubic-bezier(.39,.575,.565,1) both;
      display: flex;
      align-items: center;
      gap: 14px;
      padding: 1rem 1.5rem;
    }
    @keyframes fadeInNotif {
      0% { opacity: 0; transform: translate(-50%, -30px); }
      100% { opacity: 1; transform: translate(-50%, 0); }
    }
    .notif-popup .notif-close {
      margin-left: 18px;
      background: none;
      border: none;
      color: inherit;
      font-size: 1.5rem;
      cursor: pointer;
      opacity: 0.7;
      transition: opacity 0.2s;
    }
    .notif-popup .notif-close:hover { opacity: 1; }
  </style>
  <script>
    function closeNotifPopup() {
      var notif = document.getElementById('notif-popup-order');
      if (notif) notif.style.display = 'none';
      // Set session agar popup tidak muncul lagi setelah reload
      var xhr = new XMLHttpRequest();
      xhr.open('POST', 'unset_notif_order.php', true);
      xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
      xhr.send('unset=1');
    }
    setTimeout(closeNotifPopup, 6000);
  </script>
  <?php
  if (isset($_SESSION['user']) && empty($_SESSION['notif_order_shown'])) {
    $userId = $_SESSION['user']['id'];
    $orderQ = $conn->query("SELECT id, status_verifikasi, catatan_admin FROM orders WHERE user_id = '$userId' ORDER BY id DESC LIMIT 1");
    if ($orderQ && $orderQ->num_rows > 0) {
      $order = $orderQ->fetch_assoc();
      $icon = '';
      $bg = '';
      $border = '';
      $text = '';
      $msg = '';
      if ($order['status_verifikasi'] === 'Valid') {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>';
        $bg = 'background: #e6fbe9;';
        $border = 'border-color: #22c55e;';
        $text = 'color: #166534;';
        $msg = '<b>Pembayaran VALID.</b> Pesanan Anda sedang diproses.';
      } else if ($order['status_verifikasi'] === 'Tidak Valid') {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>';
        $bg = 'background: #fee2e2;';
        $border = 'border-color: #ef4444;';
        $text = 'color: #991b1b;';
        $catatan = $order['catatan_admin'] ? htmlspecialchars($order['catatan_admin']) : 'Silakan cek kembali bukti pembayaran Anda.';
        $msg = '<b>Pembayaran TIDAK VALID.</b> ' . $catatan;
      } else if ($order['status_verifikasi'] === 'Menunggu Pembayaran') {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01" /></svg>';
        $bg = 'background: #fef9c3;';
        $border = 'border-color: #eab308;';
        $text = 'color: #854d0e;';
        $msg = '<b>Belum Dibayar.</b> Segera lakukan pembayaran untuk memproses pesanan Anda!';
      } else if ($order['status_verifikasi'] === 'Menunggu Verifikasi') {
        $icon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg>';
        $bg = 'background: #e0f2fe;';
        $border = 'border-color: #38bdf8;';
        $text = 'color: #075985;';
        $msg = '<b>Pembayaran menunggu verifikasi admin.</b>';
      }
      if ($msg) {
        echo '<div id="notif-popup-order" class="notif-popup" style="' . $bg . $border . $text . '">' . $icon . '<span>' . $msg . '</span><button class="notif-close" onclick="closeNotifPopup()">&times;</button></div>';
      }
    }
  }
  ?>
  <!-- Navbar -->
  <nav class="bg-black shadow-md sticky top-0 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3 flex justify-between items-center">
      <div class="flex items-center space-x-3">
        <img src="assets/uploads/logo.png" alt="Logo" class="w-10 h-10 rounded-full border-2 border-purple-500 shadow">
        <h1 class="text-2xl md:text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-500 tracking-wide">Figureku</h1>
      </div>
      <ul class="hidden md:flex space-x-8 text-white font-semibold items-center">
        <li><a href="#home" class="hover:text-fuchsia-400 transition">Home</a></li>
        <li><a href="#about" class="hover:text-fuchsia-400 transition">About Us</a></li>
        <li><a href="#catalog" class="hover:text-fuchsia-400 transition">Katalog</a></li>
        <li><a href="#testimoni" class="hover:text-fuchsia-400 transition">Testimoni</a></li>
        <?php if (isset($_SESSION['user'])): ?>
          <li class="text-fuchsia-400 font-semibold">Hi, <?= htmlspecialchars($_SESSION['user']['username']) ?></li>
          <li><a href="logout.php" class="hover:text-fuchsia-400">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php" class="hover:text-fuchsia-400">Login</a></li>
        <?php endif; ?>
      </ul>
      <div class="flex items-center space-x-3">
        <a href="cart.php" class="btn-main text-sm flex items-center gap-2"><span>🛒</span> Keranjang</a>
        <button class="md:hidden text-3xl text-purple-400 focus:outline-none" @click="open = !open">&#9776;</button>
      </div>
    </div>
  </nav>


  <!-- Notifikasi Status Pesanan User (jika login) -->
  <?php
    if (isset($_SESSION['user'])) {
      $userId = $_SESSION['user']['id'];
      $orderQ = $conn->query("SELECT status_verifikasi, catatan_admin FROM orders WHERE user_id = '$userId' ORDER BY id DESC LIMIT 1");
      if ($orderQ && $orderQ->num_rows > 0) {
        $order = $orderQ->fetch_assoc();
        $status = $order['status_verifikasi'];
        $catatan = $order['catatan_admin'];
        $msg = '';
        $color = '';
        // Hapus seluruh notifikasi duplikat lama, tidak perlu echo apapun di sini
      }
    }
  ?>
  <!-- Hero Section with Slideshow -->
  <section id="home" class="w-full min-h-screen flex flex-col justify-center items-center relative overflow-hidden text-center">
    <div id="hero-slideshow" class="absolute inset-0 w-full h-full z-0">
      <img src="assets/uploads/Gemini_Generated_Image_x0pz7rx0pz7rx0pz.png" class="hero-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000" style="opacity:1;" />
      <img src="assets/uploads/Gemini_Generated_Image_vsz2zgvsz2zgvsz2.png" class="hero-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000" />
      <img src="assets/uploads/Gemini_Generated_Image_x0pz7rx0pz7rx0pz.png" class="hero-slide absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-1000" />
    </div>
    <div class="relative z-10 flex flex-col justify-center items-center min-h-screen w-full bg-gradient-to-b from-black/80 via-black/60 to-black/80">
      <div class="glass px-8 py-10 md:py-16 text-center max-w-2xl mx-auto">
        <h2 class="text-5xl md:text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-300 to-fuchsia-400 mb-4 drop-shadow-lg">Selamat Datang di FigureKu</h2>
        <p class="text-lg md:text-xl text-purple-200 mb-6 drop-shadow">Temukan action figure terbaik & original di sini!</p>
        <a href="#catalog" class="btn-main text-lg inline-block mt-2">Lihat Katalog</a>
      </div>
    </div>
  </section>

  <script>
    // Simple JS slideshow for hero section
    document.addEventListener('DOMContentLoaded', function() {
      const slides = document.querySelectorAll('#hero-slideshow .hero-slide');
      let current = 0;
      setInterval(() => {
        slides[current].style.opacity = 0;
        current = (current + 1) % slides.length;
        slides[current].style.opacity = 1;
      }, 3500);
    });
  </script>

  <!-- About Section -->
<section id="about" class="section w-full max-w-screen-xl mx-auto px-6 py-16">
  <div class="relative grid md:grid-cols-2 gap-16 items-center">
    <!-- Dekorasi background animasi -->
    <div class="absolute -top-10 -left-10 w-60 h-60 bg-gradient-to-br from-fuchsia-500/30 to-purple-700/30 rounded-full blur-2xl z-0 animate-pulse"></div>
    <div class="absolute bottom-0 right-0 w-40 h-40 bg-gradient-to-tr from-purple-400/20 to-fuchsia-400/20 rounded-full blur-2xl z-0 animate-pulse"></div>
    <!-- Model 3D + Ornaments -->
    <div class="w-full h-[400px] bg-gradient-to-br from-purple-900/80 to-fuchsia-900/80 rounded-2xl flex items-center justify-center shadow-xl relative z-10 overflow-hidden">
      <model-viewer 
        src="assets/models/figure.glb" 
        alt="3D Action Figure"
        auto-rotate 
        camera-controls 
        background-color="transparent"
        shadow-intensity="1"
        disable-zoom
        style="width: 100%; height: 100%;"
        exposure="1"
      ></model-viewer>
      <div class="absolute top-4 left-4 bg-gradient-to-r from-fuchsia-600 to-purple-600 text-white text-xs px-4 py-1 rounded-full font-bold shadow-lg flex items-center gap-2 animate-bounce">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
        Trusted Seller
      </div>
      <!-- Ornaments -->
      <div class="absolute left-10 top-10 animate-float-slow z-20">
        <svg width="32" height="32" fill="none" viewBox="0 0 32 32"><circle cx="16" cy="16" r="6" fill="#e879f9" fill-opacity="0.25"/></svg>
      </div>
      <div class="absolute right-10 bottom-10 animate-float-fast z-20">
        <svg width="24" height="24" fill="none" viewBox="0 0 24 24"><circle cx="12" cy="12" r="4" fill="#a78bfa" fill-opacity="0.18"/></svg>
      </div>
      <div class="absolute left-1/2 top-2/3 animate-float-mid z-20">
        <svg width="18" height="18" fill="none" viewBox="0 0 18 18"><circle cx="9" cy="9" r="2" fill="#fff" fill-opacity="0.12"/></svg>
      </div>
    </div>
    <!-- Teks Tentang Kami -->
    <div class="text-purple-100 px-2 relative z-10 flex flex-col items-start text-left max-w-2xl mx-auto md:mx-0">
      <h3 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-400 mb-4 flex items-center gap-2">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-fuchsia-400 animate-spin-slow" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" fill="none" /></svg>
        Tentang Kami
      </h3>
      <p class="leading-relaxed text-lg mb-6">
        <strong class="text-white">FigureKu</strong> adalah toko daring (e-commerce) yang berfokus menjual berbagai macam <span class="font-semibold italic text-fuchsia-200">action figure original</span> dari berbagai franchise populer seperti anime, film, game, dan tokoh superhero.
      </p>
      <div class="flex flex-wrap gap-3 mb-6">
        <span class="flex items-center gap-2 bg-fuchsia-700/80 text-white px-3 py-1 rounded-full text-xs font-semibold shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" /></svg> 100% Original</span>
        <span class="flex items-center gap-2 bg-purple-700/80 text-white px-3 py-1 rounded-full text-xs font-semibold shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4" /></svg> Garansi Uang Kembali</span>
        <span class="flex items-center gap-2 bg-fuchsia-500/80 text-white px-3 py-1 rounded-full text-xs font-semibold shadow"><svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h2l1 2h13l1-2h2" /></svg> Pengiriman Cepat</span>
      </div>
      <div class="flex flex-row gap-10 mb-6 w-full">
        <div class="flex flex-col items-start">
          <span class="text-3xl md:text-4xl font-extrabold text-fuchsia-400 drop-shadow">500+</span>
          <span class="text-xs text-purple-200 font-semibold mt-1">Produk Original</span>
        </div>
        <div class="flex flex-col items-start">
          <span class="text-3xl md:text-4xl font-extrabold text-fuchsia-400 drop-shadow">1000+</span>
          <span class="text-xs text-purple-200 font-semibold mt-1">Pelanggan Puas</span>
        </div>
        <div class="flex flex-col items-start">
          <span class="text-3xl md:text-4xl font-extrabold text-fuchsia-400 drop-shadow">4.9/5</span>
          <span class="text-xs text-purple-200 font-semibold mt-1">Rating Toko</span>
        </div>
      </div>
      <p class="leading-relaxed text-base mb-3">
        Kami berkomitmen untuk menyediakan produk dengan kualitas terbaik, orisinalitas terjamin, dan harga yang kompetitif. Semua produk kami dipilih secara selektif dan bekerja sama dengan distributor resmi dari Jepang, Amerika, dan negara lainnya.
      </p>
      <p class="leading-relaxed text-base mb-7">
        Selain itu, FigureKu juga mendukung komunitas kolektor Indonesia dengan menyediakan figur eksklusif, pre-order, hingga produk buatan tangan (<em>custom</em>).
      </p>
      <a href="#catalog" class="btn-main text-base shadow-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>Lihat Koleksi</a>
    </div>
  </div>
  <style>
    @keyframes spin-slow { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
    .animate-spin-slow { animation: spin-slow 6s linear infinite; }
    @keyframes float-slow { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-18px);} }
    .animate-float-slow { animation: float-slow 5s ease-in-out infinite; }
    @keyframes float-fast { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-10px);} }
    .animate-float-fast { animation: float-fast 2.5s ease-in-out infinite; }
    @keyframes float-mid { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-8px);} }
    .animate-float-mid { animation: float-mid 3.5s ease-in-out infinite; }
  </style>
</section>



  <!-- Katalog Produk -->
  <section id="catalog" class="section container mx-auto px-6 py-12" x-data="{ modalOpen: false, modalImage: '' }">
    <h3 class="text-3xl font-extrabold mb-10 text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-400 tracking-wide">Katalog Produk</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-10">
      <?php
      $result = $conn->query("SELECT * FROM products");
      while ($row = $result->fetch_assoc()):
      ?>
      <div class="glass product-card rounded-2xl shadow-lg p-6 hover:scale-105 hover:shadow-2xl transition-all duration-300 flex flex-col justify-between relative group">
        <div class="overflow-hidden rounded-xl mb-4 cursor-pointer border-2 border-purple-700 hover:border-fuchsia-400 transition aspect-square bg-gray-800 flex items-center justify-center" style="min-height:220px;" @click="modalOpen = true; modalImage = 'assets/uploads/<?= $row['image'] ?>'">
          <img 
            src="assets/uploads/<?= $row['image'] ?>" 
            class="w-full h-full object-cover object-center transition-transform duration-300 group-hover:scale-110"
            alt="<?= $row['name'] ?>"
            loading="lazy"
          >
        </div>
        <h4 class="text-2xl font-extrabold text-center text-purple-100 mb-1 tracking-wide"><?= $row['name'] ?></h4>
        <p class="text-base text-purple-200 mb-3 text-center line-clamp-2 min-h-[40px]"><?= $row['description'] ?></p>
        <div class="flex justify-center gap-2 mb-3">
          <span class="inline-block bg-gradient-to-r from-fuchsia-600 to-purple-600 text-white text-xs px-4 py-1 rounded-full font-semibold shadow">Stock: <?= $row['stock'] ?></span>
        </div>
        <p class="text-fuchsia-400 font-bold text-2xl mb-4 text-center drop-shadow">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
        <form method="post" action="cart.php">
          <input type="hidden" name="product_id" value="<?= $row['id'] ?>">
          <input type="hidden" name="action" value="add">
          <div class="flex justify-center mt-2">
            <button class="btn-main text-base flex items-center gap-2 shadow-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-1.35 2.7A2 2 0 007.48 19h9.04a2 2 0 001.83-2.3L17 13M9 21h6" /></svg>Tambah ke Keranjang</button>
          </div>
        </form>
        <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
          <button @click="modalOpen = true; modalImage = 'assets/uploads/<?= $row['image'] ?>'" class="bg-white/80 hover:bg-fuchsia-200 text-fuchsia-700 rounded-full p-2 shadow-lg"><svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A2 2 0 0020 6.382V5a2 2 0 00-2-2H6a2 2 0 00-2 2v1.382a2 2 0 00.447 1.342L9 10m6 0v10a2 2 0 01-2 2H9a2 2 0 01-2-2V10m6 0H9" /></svg></button>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <!-- Modal -->
    <div 
      x-show="modalOpen" 
      class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-80 z-50"
      x-transition
      x-cloak
    >
      <div class="glass p-6 rounded-2xl shadow-2xl max-w-2xl w-full relative border-2 border-fuchsia-400">
        <button @click="modalOpen = false" class="absolute top-2 right-2 text-fuchsia-500 text-3xl font-extrabold hover:scale-125 transition">&times;</button>
        <img :src="modalImage" alt="Preview" class="w-full h-auto rounded-xl shadow-lg">
      </div>
    </div>
  </section>
  <div class="my-8 border-t border-gray-700"></div>

  <!-- Testimoni -->
  <section id="testimoni" class="section container mx-auto px-6 py-12 text-center">
    <h3 class="text-3xl font-extrabold mb-10 text-center text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-fuchsia-400 tracking-wide">Testimoni Pelanggan</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <div class="glass p-8 rounded-2xl shadow-lg hover:scale-105 transition flex flex-col items-center">
        <img src="https://randomuser.me/api/portraits/men/32.jpg" class="w-16 h-16 rounded-full border-2 border-fuchsia-400 mb-3 shadow">
        <p class="text-purple-100 italic mb-3">"Figureku punya banyak pilihan karakter favoritku. Kualitasnya top!"</p>
        <h4 class="font-semibold text-fuchsia-400">— Riko, Jakarta</h4>
      </div>
      <div class="glass p-8 rounded-2xl shadow-lg hover:scale-105 transition flex flex-col items-center">
        <img src="https://randomuser.me/api/portraits/women/44.jpg" class="w-16 h-16 rounded-full border-2 border-fuchsia-400 mb-3 shadow">
        <p class="text-purple-100 italic mb-3">"Pengirimannya aman, packing rapi. Barang sampai tanpa lecet!"</p>
        <h4 class="font-semibold text-fuchsia-400">— Anya, Bandung</h4>
      </div>
      <div class="glass p-8 rounded-2xl shadow-lg hover:scale-105 transition flex flex-col items-center">
        <img src="https://randomuser.me/api/portraits/men/65.jpg" class="w-16 h-16 rounded-full border-2 border-fuchsia-400 mb-3 shadow">
        <p class="text-purple-100 italic mb-3">"Suka banget sama koleksi eksklusifnya. Harganya juga bersaing."</p>
        <h4 class="font-semibold text-fuchsia-400">— Dimas, Surabaya</h4>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-black text-center py-6 mt-10 border-t border-gray-800">
    <p class="text-sm text-purple-400">&copy; <?= date('Y') ?> <span class="font-bold text-fuchsia-400">Figureku</span>. All rights reserved.</p>
  </footer>

  <!-- GSAP Scroll Animation -->
  <script>
    gsap.registerPlugin(ScrollTrigger);
    gsap.utils.toArray(".section").forEach((section) => {
      gsap.from(section, {
        y: 50,
        opacity: 0,
        duration: 1,
        ease: "power2.out",
        scrollTrigger: {
          trigger: section,
          start: "top 80%",
          toggleActions: "play none none none"
        }
      });
    });
  </script>

</body>
</html>
