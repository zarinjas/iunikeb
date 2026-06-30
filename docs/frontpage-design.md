# KOPERASI UNIKEB FRONTPAGE MASTER PROMPT
Version: 1.0

---

# OBJECTIVE

Bina semula Frontpage Website Koperasi UNIKEB berdasarkan design reference yang diberikan.

PENTING:

- Kekalkan layout hampir 100% sama seperti design reference.
- Jangan ubah susunan section.
- Jangan tukar konsep design.
- Jangan tambah section yang tidak diperlukan.
- Jangan hasilkan design baru.
- Tujuan adalah recreate semula design reference dengan code yang lebih moden, responsive dan clean.

Content boleh datang daripada database tetapi layout mesti mengikut design reference.

Design perlu premium, moden, corporate, kemas dan professional.

---

# TECH STACK

Frontend:

- HTML5
- TailwindCSS
- AlpineJS (jika diperlukan)
- SwiperJS untuk Hero Slider

Backend:

Laravel

Template:

Blade

Semua data mestilah Dynamic.

Hardcode hanya sebagai placeholder.

---

# DESIGN SYSTEM

Container

1280px

---

Section Spacing

Desktop

80px

Tablet

60px

Mobile

40px

---

Border Radius

Card

16px

Button

12px

Image

16px

---

Shadow

Soft Shadow sahaja.

Jangan terlalu tebal.

---

Animation

Fade Up

Hover Lift

Fade In

Smooth Transition

Duration:

300ms

---

Typography

Font:

Inter

Heading

700

Body

400

Button

600

---

COLOR

Primary

#0F8F83

Dark

#0F172A

Background

#F8FAFC

White

#FFFFFF

Border

#E5E7EB

Text Secondary

#6B7280

---

# PAGE STRUCTURE

01 Header

02 Hero Slider

03 Statistics

04 Services

05 Benefit Banner

06 Business Section

07 Promotion Section

08 Membership CTA

09 Bottom Statistics

10 Footer

JANGAN ubah susunan ini.

---

# HEADER

Position

Sticky

Transparent semasa Hero dipaparkan.

Apabila scroll:

background putih

shadow ringan

height lebih kecil sedikit.

---

Logo

Position

Left

Dynamic.

Logo mesti diambil daripada System Setting.

Jangan hardcode.

Jika logo ditukar dalam Admin Panel, website berubah secara automatik.

---

Navigation

Center

Dynamic Menu.

Menu diambil daripada database.

Menu:

Utama

Profil

Perkhidmatan

Perniagaan

Maklumat

Hubungi

Dropdown jika diperlukan.

Hover:

Underline warna Primary.

---

Button

Right

Label

Log Masuk Admin / Ahli

Dynamic.

URL diambil daripada System Setting.

Button warna Primary.

Hover:

lebih gelap sedikit.

Ada icon login.

---

Responsive

Desktop

Menu penuh.

Tablet

Hamburger.

Mobile

Drawer Menu.

---

# HERO SECTION

Mengikut design reference.

Jangan ubah konsep.

---

Height

Desktop

760px

Tablet

650px

Mobile

600px

---

Background

Full Width Image Carousel.

Image memenuhi keseluruhan Hero.

Object Fit Cover.

Dynamic.

---

Slider

Menggunakan SwiperJS.

Auto Slide.

Delay

5000ms

Loop

True

Navigation Arrow

Kiri

Kanan

Pagination Dot

Bottom Center

Dynamic.

---

Overlay

Hitam Gradient.

Opacity

60%

Gradient:

Top

35%

Bottom

75%

Pastikan text mudah dibaca.

---

Slide Content

Position

Left

Lebih kurang 120px dari kiri.

Vertical Center.

Maximum Width

600px

---

Subtitle

Small uppercase.

Primary Color.

---

Title

Bold.

48px Desktop.

36px Tablet.

30px Mobile.

---

Description

Lebar sekitar 500px.

Warna putih.

Line Height selesa.

---

Primary Button

Ketahui Lebih Lanjut

Dynamic.

---

Secondary Button

Tonton Video

Menggunakan icon Play.

Dynamic.

---

Hero Image

Admin boleh:

Tambah

Edit

Padam

Susun

Enable

Disable

Setiap slide mempunyai:

Image

Subtitle

Title

Description

Primary Button Text

Primary URL

Secondary Button Text

Secondary URL

Status

Sort Order

---

# STATISTICS

Terletak di bawah Hero.

Card putih.

Sedikit overlap Hero.

Sama seperti reference.

Grid

4 Columns

Desktop

2 Tablet

1 Mobile

Card mempunyai:

Icon

Value

Label

Dynamic.

Contoh

26000+

Keanggotaan Aktif

7

Perniagaan Milik Koperasi

RM34.7 Juta

Jumlah Aset

Sejak 1996

Ditubuhkan

Semua Dynamic.

---

# SERVICES SECTION

Layout sama seperti reference.

Sebelah kiri

Title

Description

Button

Sebelah kanan

3 Card Services.

Desktop

1 + 3

Tablet

Stack

Mobile

Stack

---

Card

Image

Icon

Title

Description

Button

Dynamic.

Hover

Image Zoom

Shadow

Lift

---

Database

title

description

image

icon

button_text

button_url

status

sort_order

---

# BENEFIT SECTION

Dark Background.

Rounded.

Sama seperti design reference.

Sebelah kiri

Image Building

Sebelah kanan

List Benefit

Menggunakan Icon.

Benefit:

Kuasa Membeli

Manfaat Kewangan

Peluang Perniagaan

Kebajikan Ahli

Dynamic.

---

# BUSINESS SECTION

Layout mengikut reference.

Title kiri.

Button kanan.

Grid Card.

Desktop

5 Card

Tablet

3

Mobile

2

Card

Image

Business Name

Short Description

Dynamic.

Hover

Scale

Shadow

---

Database

business_name

image

description

url

status

sort_order

---

# PROMOTION SECTION

Dua card besar.

Jangan ubah layout.

Card kiri

Promosi Aplikasi iUniKEB

Ada gambar telefon.

Title

Description

Store Button

App Store

Google Play

AppGallery

Semua Dynamic.

Jika tiada URL jangan papar button.

---

Card kanan

Promosi Keahlian.

Ada gambar Kad Ahli.

Title

Description

CTA

Daftar Sekarang

Dynamic.

---

# MEMBERSHIP CTA

Background putih.

Border Radius.

Mengikut reference.

Sebelah kiri

Title

Description

Button

Sebelah kanan

4 Statistics

Dynamic.

---

Statistics

Keanggotaan Aktif

Jumlah Aset

Modal Syer

Simpanan

Semua Dynamic.

---

# FOOTER

Background Dark.

4 Column.

Jangan ubah layout.

Column 1

Logo

Description

Social Media

Dynamic.

---

Column 2

Quick Links

Dynamic Menu.

---

Column 3

Contact

Alamat

Telefon

Email

Dynamic.

---

Column 4

Operating Hours

Dynamic.

---

Bottom Footer

Copyright

Privacy Policy

Terms

Dynamic.

---

# ADMIN REQUIREMENTS

Semua section mesti mempunyai CMS.

Admin boleh:

Tambah

Edit

Padam

Susun

Enable

Disable

Upload Image

Tukar URL

Tukar Text

Tanpa perlu ubah code.

---

# IMAGE REQUIREMENTS

Gunakan lazy loading.

Semua image:

object-fit: cover;

Responsive.

Image tidak boleh stretch.

Border radius konsisten.

---

# RESPONSIVE

Desktop

1280+

Tablet

768

Mobile

390

Semua section perlu responsive tanpa mengubah konsep asal.

---

# PERFORMANCE

Hero menggunakan lazy preload.

Image optimize.

Gunakan WebP jika ada.

Minimum CLS.

Minimum Layout Shift.

---

# ACCESSIBILITY

Button mempunyai focus state.

Image mempunyai alt text.

Heading menggunakan H1 hingga H4 dengan betul.

Navigation boleh digunakan menggunakan keyboard.

---

# UI CONSISTENCY

Semua Button menggunakan style yang sama.

Semua Card menggunakan radius yang sama.

Semua Shadow konsisten.

Semua spacing konsisten.

Semua icon menggunakan style yang sama.

Jangan campurkan pelbagai jenis icon.

---

# IMPORTANT RULES

❌ Jangan redesign layout.

❌ Jangan tukar susunan section.

❌ Jangan tambah section baru.

❌ Jangan tukar konsep UI.

❌ Jangan tukar kedudukan Hero.

❌ Jangan tukar kedudukan Statistics.

❌ Jangan tukar kedudukan Services.

❌ Jangan tukar kedudukan Benefit.

❌ Jangan tukar kedudukan Business.

❌ Jangan tukar kedudukan Promotion.

❌ Jangan tukar kedudukan Membership.

❌ Jangan tukar Footer.

✅ Recreate design reference sebaik mungkin.

✅ Pixel-perfect sedekat mungkin dengan reference.

✅ Semua content Dynamic.

✅ Semua image Dynamic.

✅ Logo Dynamic daripada System Setting.

✅ Button "Log Masuk Admin / Ahli" Dynamic daripada System Setting.

✅ Hero Background menggunakan Dynamic Carousel dan Auto Slide.

✅ Gunakan kod yang bersih, modular, responsive dan mudah diselenggara.

END OF MASTER PROMPT