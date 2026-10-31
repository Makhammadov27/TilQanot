# Mustaqil loyiha hisoboti

## Mavzu

**Xorijiy tillarni o‘rganishga mo‘ljallangan TilQanot web-saytini ishlab chiqish.**

## Muammo va maqsad

Yangi so‘zlarni yodlashda tartibli takrorlash va o‘z natijasini kuzatish qiyin. TilQanot ingliz tili bo‘yicha mavzuli kurslar, qisqa darslar va o‘zini tekshiradigan lug‘at kartochkalarini bitta interfeysda beradi. Loyiha o‘quv maqsadida tayyorlangan, tashqi pulli xizmat talab qilmaydi.

## Foydalanuvchilar va vakolatlar

| Rol | Imkoniyatlar |
|---|---|
| Mehmon | Bosh sahifa, kurs katalogi va qidiruvni ko‘rish; ro‘yxatdan o‘tish; aloqa xabari yuborish |
| O‘quvchi | Kirish, so‘zlarni mashq qilish, javobni qayd etish, o‘rganish tarixini CSVga chiqarish |
| Muharrir | Dars va lug‘at yozuvlarini yaratish va tahrirlash |
| Administrator | Muharrir imkoniyatlari bilan birga dars va lug‘at yozuvlarini o‘chirish |

Hisob ochilganda rol avtomatik ravishda `student` bo‘ladi. `admin` va `operator` rollarini faqat ma’lumotlar bazasi orqali vakolatli foydalanuvchiga berish mumkin.

## Funksional talablar

1. Kurslar, darslar va lug‘at bo‘yicha mavzuli kontent ko‘rsatiladi.
2. Kurslar nomi, tavsifi va darajasi bo‘yicha qidiriladi.
3. Foydalanuvchi hisob yaratadi, tizimga kiradi va chiqadi.
4. So‘z kartochkasida inglizcha so‘z, talaffuz, o‘zbekcha tarjima va misol gap ko‘rsatiladi.
5. “Bilaman” javobidan keyin takrorlash oralig‘i bosqichma-bosqich 1, 3 va 2 baravar oshib boradi (90 kungacha); “Yana mashq qilaman” tanlansa 10 daqiqadan so‘ng qayta ko‘rish rejalashtiriladi.
6. Javoblar `review_logs` jadvalida tarix, keyingi ko‘rish va interval ma’lumoti `user_vocabulary` jadvalida saqlanadi.
7. Muharrir dars va lug‘at yozuvlarini yaratadi/tahrirlaydi; faqat administrator ularni o‘chiradi.
8. Mashq tarixi CSV sifatida yuklab olinadi; aloqa xabari bazada saqlanadi.

## 90 soatlik ish reja

Uslubiy ko‘rsatmadagi mustaqil loyiha hajmi asosida tavsiya etilgan taqsimot:

| Bosqich | Ish mazmuni | Soat | Natija |
|---|---|---:|---|
| 1 | Mavzuni tahlil qilish va texnik topshiriq | 6 | Talablar, foydalanuvchilar va qabul shartlari |
| 2 | Ma’lumotlar modeli va sahifa tuzilmasi | 8 | Jadval aloqalari va interfeys xaritasi |
| 3 | HTML/CSS/JavaScript interfeysi | 12 | Moslashuvchan sahifalar va interaktiv kartochka |
| 4 | Sof PHP MVC va MySQL ulanishi | 10 | Controller, model va view qatlamlari |
| 5 | CRUD, qidiruv va hisobot | 14 | Dars/so‘zlarni boshqarish, qidiruv, CSV |
| 6 | Kirish, rollar va xavfsizlik | 10 | Uch rol, server tekshiruvlari va CSRF |
| 7 | Fayllar, aloqa va eksport | 8 | Aloqa formasi va CSV eksport |
| 8 | Mavzuga xos jarayon | 8 | Takrorlash intervali bilan so‘z mashqi |
| 9 | Sinash, xatolarni tuzatish va joylashtirish | 8 | Sinov bayonnomasi va ishga tushirish muhiti |
| 10 | Hisobot va himoyaga tayyorgarlik | 6 | Yozma izoh va namoyish ssenariysi |
| **Jami** |  | **90** |  |

Jadval topshiriqni rejalashtirish uchun; haqiqiy sarflangan vaqt sifatida ko‘rsatilmasin. Sinov muhiti sozlangach, 9-bosqich natijalari va sana/izohlar bilan alohida sinov bayonnomasi to‘ldiriladi.

## Arxitektura

Ilova yengil MVC uslubida tashkil etilgan. `public/index.php` marshrutni tanlaydi, `app/Controllers` so‘rov va vakolatni tekshiradi, `app/Models` parametrli PDO so‘rovlarini bajaradi, `app/Views` esa sahifalarni chiqaradi. Brauzer tomoni HTML, CSS va JavaScriptdan iborat.

## Ma’lumotlar bazasi

| Jadval | Vazifasi | Asosiy bog‘lanish |
|---|---|---|
| `users` | Hisob, xeshlangan parol va rol | Bir nechta takrorlash yozuvi va kontakt xabari |
| `courses` | Til kurslari | Har bir kursda ko‘p dars |
| `lessons` | Kurs bo‘limlari | Har bir darsda ko‘p lug‘at yozuvi |
| `vocabulary` | So‘z, tarjima, misol va talaffuz | Darsga tashqi kalit |
| `review_logs` | Har bir mashq javobining tarixi | Foydalanuvchi va so‘zga tashqi kalit |
| `user_vocabulary` | Foydalanuvchi kesimidagi takrorlash rejasi | Foydalanuvchi + so‘z birlamchi kaliti |
| `contact_messages` | Aloqa formasi xabarlari | Hisob bo‘lmasa foydalanuvchi bo‘sh qolishi mumkin |

## Xavfsizlik choralari

Parollar PHP `password_hash()` bilan xeshlanadi. PDO prepared statement SQL injection ehtimolini kamaytiradi. POST amallar CSRF token bilan himoyalangan, matn HTMLda chiqarilishidan oldin escape qilinadi. Rol tekshiruvi serverda bajariladi va o‘zgartiruvchi amallar POST bilan cheklanadi. Sessiya cookie’siga HttpOnly va SameSite=Lax beriladi; HTTPS orqali ishlaganda Secure belgisi yoqiladi.

## Ishga tushirish va namoyish

O‘rnatish bosqichlari loyiha ildizidagi `README.md` da. Namoyishda avval kurslarni va qidiruvni ko‘rsatish, keyin foydalanuvchi ochish, so‘z kartochkasida javob berish, CSV eksport qilish va administrator panelida yangi lug‘at so‘zi qo‘shib/o‘chirish tavsiya etiladi.

## Sinov ssenariylari

| Ssenariy | Kutiladigan natija |
|---|---|
| Noto‘g‘ri email yoki 8 belgidan qisqa parol | Forma xatoni ko‘rsatadi, hisob ochilmaydi |
| Yangi foydalanuvchi ro‘yxatdan o‘tadi | Hisob `student` roli bilan saqlanadi |
| Noto‘g‘ri token bilan POST yuboriladi | Amal bajarilmaydi va 419 javobi qaytadi |
| Student `/admin` sahifasiga o‘tadi | 403 javobi va ruxsat yo‘qligi ko‘rinadi |
| O‘quvchi “Bilaman”ni tanlaydi | Javob tarixi va rejalashtirilgan keyingi sana saqlanadi |
| O‘quvchi “Yana mashq qilaman”ni tanlaydi | Keyingi takrorlash 10 daqiqaga belgilanadi |
| Administrator yangi so‘z qo‘shadi | Yozuv bazaga saqlanib lug‘atda ko‘rinadi |
| Aloqa formasida noto‘g‘ri xabar yuboriladi | Xato matni ko‘rsatiladi va xabar saqlanmaydi |

## Cheklovlar va keyingi takomillashtirish

Hozirgi loyiha ta’lim uchun namuna: unda haqiqiy pochtaga xabar yuborish, kurs to‘lovi yoki administratorni web orqali ro‘yxatdan o‘tkazish yo‘q. Keyingi bosqichda dars/kurs tahrirlash, audio talaffuz, kunlik bildirishnoma va administrator uchun foydalanuvchilar statistikasi qo‘shilishi mumkin.
