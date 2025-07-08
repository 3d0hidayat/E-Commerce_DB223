# Figureku - Website E-Commerce Action Figure

**Figureku** adalah website e-commerce berbasis PHP yang menjual berbagai macam **action figure original** dari anime, film, dan game. Website ini menampilkan katalog produk dari database, testimoni pelanggan, serta model 3D interaktif.

## 📦 Fitur Utama

- ✅ Katalog produk dinamis dari database (MySQL)
- ✅ User login & session
- ✅ Add to cart
- ✅ Modal preview gambar produk
- ✅ Model 3D `.glb` interaktif (tanpa background)
- ✅ Animasi scroll menggunakan GSAP
- ✅ Responsif dan modern dengan Tailwind CSS
- ✅ Komponen interaktif via Alpine.js

---

## 🛠️ Teknologi yang Digunakan

| Teknologi     | Fungsi                                      |
|---------------|---------------------------------------------|
| PHP           | Backend & session                           |
| MySQL         | Database produk & user                      |
| Tailwind CSS  | Desain dan styling responsif                |
| Alpine.js     | Interaktivitas sederhana (modal, toggle)    |
| GSAP + ScrollTrigger | Animasi saat scroll                  |
| model-viewer  | Menampilkan model 3D `.glb` interaktif      |

---

## 📌 Tips

- Pastikan `.glb` tidak memiliki background hitam. Jika perlu, edit di Blender atau gunakan `.glb` dengan **material transparent**.
- Untuk performa maksimal, kompres gambar & model.
- Animasi scroll dapat disesuaikan di blok `gsap.registerPlugin(ScrollTrigger);`

---

## 👨‍💻 Author

**Figureku Project**  
 
Email: edohidayat345@gmail.com

---

## 📄 Lisensi

Proyek ini bebas digunakan untuk pembelajaran atau pengembangan non-komersial. Hubungi author untuk izin penggunaan komersial.