<?php

/*
 * Reach Angle content, written by hand (no API), 2026-10-07.
 *
 * Applied by `php artisan angles:import-content --email=...` as a new batch
 * per angle, so it becomes the batch the angle page shows; older batches stay.
 * Each entry only applies if the angle still belongs to that user and still
 * carries `match_title`.
 *
 * Voice: Hana (texting-casual Malay, saya/you, one emoji softener, everyday
 * English where Malaysians use it, Caruman not premium, few dashes).
 * Facts: only from published drtakaful.com pages. No invented statistics or
 * client stories: "story" pieces are a scenario framed as one ("Bayangkan"),
 * a question Hana really gets, or Hana's own process.
 */

return [

    6 => [
        'match_title' => 'Crtitical Illness Awareness',
        'casual'  => "Bapak-bapak anak 3, soalan je 🙂 Kalau esok doktor cakap kena rehat setahun sebab sakit kritikal, gaji siapa yang masuk? Medical card settle bil hospital, tapi dia bukan ganti gaji.",
        'story'   => "Bayangkan: umur 36, anak 3, gaji RM5,000. Satu pagi kena strok ringan, doktor minta rehat 12 bulan. Bil hospital settle dengan medical card, tapi sewa rumah, kereta, yuran sekolah tetap jalan setiap bulan. Pelan sakit kritikal bayar lump sum masa tu, supaya you boleh fokus pulih, bukan fikir duit.",
        'factual' => "Bila kena sakit kritikal macam kanser atau strok, doktor boleh minta rehat 12 hingga 24 bulan. Cara kira cover yang saya guna: gaji sebulan x 24 bulan. Gaji RM5,000 = cover sekitar RM120,000, cukup untuk 2 tahun pulih tanpa stress kewangan.",
    ],

    7 => [
        'match_title' => 'The "What If" Jab',
        'casual'  => "Satu soalan untuk malam ni: kalau gaji berhenti esok, simpanan you boleh tahan family berapa bulan? Kira betul-betul, bukan agak 🙂 Nombor tu la jawapan sama ada you perlukan perlindungan atau tak.",
        'story'   => "Soalan ni saya selalu tanya masa jumpa parents muda: \"Kalau you tak boleh kerja bulan depan, family guna duit apa?\" Kebanyakan diam sekejap, lepas tu kira atas kertas. Selalunya jawapan dia lebih pendek dari yang dia sangka. Lepas nampak nombor sendiri, baru perbincangan tu jadi serius.",
        'factual' => "Formula mudah: simpanan ÷ komitmen bulanan = berapa bulan family boleh bertahan. Simpanan RM15,000, komitmen RM3,200 sebulan = kurang 5 bulan. Sakit kritikal pulak boleh perlukan rehat 12 hingga 24 bulan. Beza antara 5 bulan dan 24 bulan tu la gap yang perlu ditutup.",
    ],

    8 => [
        'match_title' => 'Hospital Bill Reality Check',
        'casual'  => "Teka: masuk wad hospital swasta sebab appendix, berapa bil dia? RM2k? RM5k? 🤔 Jawapan dia boleh cecah RM10k hingga RM15k, untuk satu admission je.",
        'story'   => "Bayangkan tengah malam perut sakit teruk, terus ke hospital swasta. Sebelum masuk wad, kaunter minta deposit dulu, boleh cecah RM3,500. Lepas pembedahan appendix, bil akhir RM10k lebih. Soalan dia bukan mampu ke tak, tapi duit siapa yang habis dulu: simpanan, KWSP, atau medical card.",
        'factual' => "Angka hospital swasta yang ramai tak tahu: appendix RM10k hingga RM15k, pembedahan pintasan jantung RM60,000 hingga RM80,000, deposit masuk wad boleh cecah RM3,500. Medical card bukan untuk demam klinik RM50, ia untuk bil macam ni.",
    ],

    9 => [
        'match_title' => 'EPF Withdrawal Trap',
        'casual'  => "KWSP tu duit pencen you, bukan tabung hospital 🙂 Setiap ringgit yang keluar untuk bil hospital hari ni, tu ringgit yang tak sempat membesar untuk umur 60 nanti.",
        'story'   => "Bayangkan kerja 20 tahun, KWSP dah cantik. Kena sakit, bil hospital besar, takde medical card, jadi keluarkan KWSP untuk bayar. Sembuh, balik kerja, tapi duit pencen dah berkurang dan masa untuk kumpul semula dah tak banyak. Medical card ialah benteng supaya KWSP tak perlu disentuh.",
        'factual' => "Bila tiada medical card, bil hospital besar biasanya dibayar ikut urutan ni: simpanan, KWSP, pinjaman keluarga. Medical card dengan had tahunan RM1.5 juta++ bayar bil tu dulu, jadi KWSP kekal untuk tujuan asal dia: persaraan.",
    ],

    10 => [
        'match_title' => "Ta'awun — Tolong-Menolong yang Smart",
        'casual'  => "Takaful ni konsep dia tolong-menolong (ta'awun) 🤝 Peserta sumbang ke satu tabung, dan bila ada yang ditimpa musibah, tabung tu bantu dia. Sama semangat macam gotong-royong, cuma untuk perlindungan kewangan.",
        'story'   => "Soalan yang saya selalu dapat: \"Takaful dengan insurans ni sama je kan, cuma nama lain?\" Saya terangkan, dalam takaful caruman masuk ke tabung bersama atas dasar tabarru' (derma), dan syarikat jadi pengurus, bukan pemilik risiko. Bila faham konsep tu, ramai rasa lebih tenang nak mula.",
        'factual' => "3 beza takaful yang ramai tak tahu: 1) kontrak berasaskan ta'awun dan tabarru', bukan jual beli risiko, 2) patuh syariah, bebas riba, 3) lebihan tabung boleh diagihkan semula kepada peserta ikut terma kontrak. Dan takaful bukan untuk orang Islam je, semua boleh sertai.",
    ],

    11 => [
        'match_title' => 'The Breadwinner Calculator',
        'casual'  => "Cuba kira sekarang: gaji sebulan x 12 x 10. Tu nilai pendapatan you untuk 10 tahun akan datang 💭 Kalau satu hari pendapatan tu berhenti, family you ada pelan untuk ganti berapa dari angka tu?",
        'story'   => "Masa sesi dengan klien, saya tak bagi angka. Saya bagi kalkulator dan minta dia kira sendiri: gaji x 12 x 10. Gaji RM4,000 jadi RM480,000. Angka yang kita kira sendiri selalunya lebih susah nak abaikan dari angka yang orang lain sebut.",
        'factual' => "Pendapatan 10 tahun pencari nafkah tunggal: gaji RM3,000 = RM360,000, RM5,000 = RM600,000, RM8,000 = RM960,000. Hibah takaful RM350,000 boleh bermula sekitar RM45 sebulan ikut umur dan tempoh, dan bayar terus kepada penama tanpa tunggu urusan pusaka.",
    ],

    12 => [
        'match_title' => 'Freelancer Blind Spot',
        'casual'  => "Freelancer, content creator, gig worker: takde boss, takde EPF automatik, SOCSO kena daftar sendiri 🙂 Bila you sakit, income pun berhenti sekali. Siapa cover you?",
        'story'   => "Bayangkan you content creator, income dari affiliate dan brand deal. Satu bulan kena masuk hospital, takde post, takde live, income terus RM0. Orang makan gaji masih ada cover company dan MC bergaji, tapi you takde semua tu. Sebab tu freelancer perlukan perlindungan sendiri lebih dari orang lain.",
        'factual' => "Kerja sendiri bermaksud: tiada caruman EPF automatik, SOCSO tidak automatik dan perlu daftar sendiri, tiada medical card company. i-Saraan bagus untuk simpanan persaraan, tapi bukan untuk emergency. Medical card dan pelan sakit kritikal ialah benda pertama yang patut ada.",
    ],

    13 => [
        'match_title' => 'The C-Word Conversation',
        'casual'  => "Oktober ni bulan kesedaran kanser payudara 🎗️ Sebelum cakap pasal takaful, satu je saya nak minta: buat pemeriksaan sendiri bulan ni, dan ajak seorang kawan buat sekali 💗",
        'story'   => "Ramai wanita yang saya jumpa ada medical card, tapi bila saya tanya, \"Kalau kena rawatan kanser dan perlu rehat setahun, siapa bayar komitmen bulanan?\", jawapan dia selalunya senyap. Medical card bantu bil hospital, tapi bukan ganti gaji. Bukan nak takutkan, cuma nak kita rancang supaya masa tu kita boleh fokus pada satu benda je: sembuh.",
        'factual' => "Kanser payudara antara kanser paling biasa dalam kalangan wanita di Malaysia, dan dikesan awal beri peluang pulih yang lebih baik. Ada pelan sakit kritikal yang boleh bayar dari peringkat awal dengan modul tambahan, tak perlu tunggu peringkat lanjut. Medical card untuk bil, pelan CI untuk gaji masa pulih.",
    ],

    14 => [
        'match_title' => 'Keyman Takaful untuk Boss Kecil',
        'casual'  => "Boss, satu soalan je: kalau esok boss kena masuk hospital 3 bulan, kedai siapa jaga? 🏪 Sewa, gaji pekerja, stok, semua tetap jalan walaupun boss tak boleh kerja.",
        'story'   => "Bayangkan boss kedai hardware, semua urusan pembekal dan akaun dia pegang sendiri. Kena sakit, kena rehat lama. Pekerja masih datang, sewa masih kena bayar, tapi jualan jatuh sebab takde siapa buat keputusan. Lump sum dari pelan sakit kritikal boleh jadi duit operasi sampai boss kembali.",
        'factual' => "Pemilik bisnes kecil: tiada cover company, EPF tak automatik, SOCSO kena daftar sendiri. Kalau ada rakan kongsi, hibah silang pastikan yang hidup ada tunai untuk beli saham daripada waris, dan bisnes terus berjalan. Kalau solo, pelan sakit kritikal dan hibah biasa jadi asas.",
    ],

    15 => [
        'match_title' => 'The First Paycheck Moment',
        'casual'  => "Tahniah gaji pertama! 🎉 Sebelum semua pergi ke iPhone baru, cuba asingkan 10% dulu untuk perlindungan. Umur muda dan sihat ialah masa caruman paling murah, dan paling senang diterima.",
        'story'   => "Masa gaji pertama, kebanyakan kita fikir nak beli apa dulu. Jarang ada yang fikir: \"Kalau aku sakit tahun ni, siapa bayar?\" Ambil perlindungan sekarang, caruman dikunci ikut umur muda, dan masa sihat, permohonan selalunya lulus tanpa exclusion. Tunggu sampai dah ada sakit, pintu tu boleh jadi lebih sempit.",
        'factual' => "Formula 10%: gaji RM2,800, bajet perlindungan RM280 sebulan. Itu cukup untuk mula dengan medical card dan perlindungan asas, dan ada baki untuk simpanan. Setiap tahun tunggu, caruman makin tinggi dan risiko dapat penyakit pun meningkat.",
    ],

    16 => [
        'match_title' => 'Warm Referral dari Klien Sedia Ada',
        'casual'  => "Hi [Nama] 🙂 Nak tanya satu soalan je: dalam family atau kawan rapat you, ada tak sesiapa yang you risau sebab dia belum ada apa-apa perlindungan? Bukan nak jual, cuma kalau you nak, saya boleh terangkan pada dia dengan cara yang sama saya terangkan pada you.",
        'story'   => "Klien saya selalu cakap benda yang sama lepas daftar: \"Lega dah ada, tapi adik aku belum ada lagi.\" Rasa risau tu tanda sayang. Sebab tu saya tanya, siapa 3 orang yang you nak pastikan terlindung, dan saya tolong bersembang dengan dia, tanpa paksaan.",
        'factual' => "Rujukan dari orang yang dah percaya cara kita kerja ialah cara paling mudah orang mula ambil perlindungan, sebab orang lebih percaya kawan dan keluarga dari ejen. Satu soalan je cukup: siapa 3 orang dalam circle you yang belum ada perlindungan?",
    ],

];
