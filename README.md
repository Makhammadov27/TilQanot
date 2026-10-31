# TilQanot — xorijiy tilni o‘rganish sayti

## Texnologiyalar

- HTML5, CSS3 va JavaScript
- O‘rganish sahifasi: React 19 va Material UI 9 (Vite orqali yig‘ilgan; tayyor JS paketi `public/assets/build/` ichida)
- Sof PHP 8.1+ (MVC uslubidagi Controller / Model / View ajratilishi)
- MySQL 8+ va PDO prepared statements, `utf8mb4`
- Sessiya asosidagi autentifikatsiya, `password_hash`, CSRF token va foydalanuvchi rollari

## Ishga tushirish (XAMPP)

1. XAMPP/Apache, PHP 8.1+ (`pdo_mysql`, `mbstring`) va MySQL/MariaDB ni o‘rnating.
2. yuklab olingan `TilQanot` loyiha papkasini XAMPP `htdocs` ichiga ko‘chiring (yoki virtual host ildizini `public/` papkaga qarating).
3. phpMyAdmin yoki MySQL terminalida `database/schema.sql` faylini import qiling. U `tilqanot` bazasini yaratib, namunaviy kurslar, darslar va lug‘atni qo‘shadi.
4. `config/config.example.php` faylidan `config/config.php` nusxa yarating. MySQL host, baza, foydalanuvchi va parolni sozlang. `config/config.php` maxfiy muhit sozlamasi hisoblanadi va versiya nazoratiga kiritilmasligi kerak.
5. Brauzerda `http://localhost/tilqanot/public/` manzilini oching.
6. Ro‘yxatdan o‘ting. Administrator paneli kerak bo‘lsa, o‘z hisobingizni MySQL orqali administratorga ko‘taring:

   ```sql
   UPDATE users SET role='admin' WHERE email='sizning@email.uz';
   -- Muharrir roli: UPDATE users SET role='operator' WHERE email='muharrir@email.uz';
   ```

## Asosiy imkoniyatlar

- Kurslarni ko‘rish va sarlavha/tavsif/daraja bo‘yicha qidirish.
- Ro‘yxatdan o‘tish, kirish va chiqish; parolni xeshlangan holda saqlash.
- Dars bo‘yicha yoki umumiy so‘z kartochkalarida mashq qilish; “Bilaman” va “Yana mashq qilaman” javoblarini saqlash.
- Muharrir dars/so‘z yaratib tahrirlaydi; administrator qo‘shimcha ravishda o‘chira oladi.
- O‘rganish tarixini CSV ko‘rinishida eksport qilish.
- Aloqa xabarini validatsiyadan so‘ng bazaga saqlash.

## MVC va ma’lumotlar modeli

`public/index.php` — yagona kirish nuqtasi; `app/Controllers` so‘rovlarni boshqaradi, `app/Models` PDO orqali bazaga murojaat qiladi, `app/Views` HTML ko‘rinishlarini beradi. Bazada `users`, `courses`, `lessons`, `vocabulary`, `review_logs`, `user_vocabulary`, `contact_messages` jadvallari mavjud. Bog‘lanishlar tashqi kalitlar bilan himoyalangan.

## Xavfsizlik va qabul shartlari

- Har bir o‘zgartiruvchi POST so‘rovida CSRF token tekshiriladi; GET so‘rovi bilan ma’lumot o‘chirilmaydi.
- SQL qiymatlari PDO parametrlariga bog‘lanadi; ko‘rsatiladigan foydalanuvchi qiymatlari HTML uchun escape qilinadi.
- Ruxsat tekshiruvi serverda amalga oshadi: muharrir dars/so‘z qo‘shib tahrirlaydi, o‘chirish administratorga beriladi.
- Parol kamida 8 belgidan iborat; email formati va majburiy maydonlar tekshiriladi.
- Qabul sinovlari: hisob yaratish/kirish; noto‘g‘ri parol rad etilishi; kurs qidiruvi; kartochkada javob saqlanishi; administrator bo‘lmagan foydalanuvchida boshqaruv paneli 403 berishi; administratorning so‘z qo‘shishi/o‘chirishi; CSV yuklab olish; noto‘g‘ri forma qiymati saqlanmasligi.

## Papkalar

```text
public/                Brauzerdan ochiladigan front controller va assetlar
app/Core/              PDO ulanishi va umumiy controller
app/Controllers/       Sahifa va POST so‘rovlarining boshqaruvi
app/Models/            Ma’lumotlar bazasi amallari
app/Views/             HTML ko‘rinishlari
config/                Mahalliy muhit sozlamalari
database/schema.sql    Jadval tuzilmasi va sinov ma’lumotlari
docs/                  Hisobot uchun loyiha izohi
```

MUI interfeys manbasini o‘zgartirib qayta yig‘ish uchun Node.js o‘rnating, loyiha ichida `npm install` va `npm run build` buyruqlarini bajaring. Oddiy XAMPP orqali foydalanish uchun build fayllari allaqachon berilgan; Node.js ishga tushirishda talab qilinmaydi.


