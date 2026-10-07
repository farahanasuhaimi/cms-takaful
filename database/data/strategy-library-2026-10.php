<?php

/*
 * Strategy Library refresh, 2026-10-07.
 *
 * Applied by `php artisan strategies:refresh-library --email=...`. Each entry
 * targets one existing strategy by id, and only if it still belongs to that
 * user and still carries `match_title` (so a strategy edited since the
 * 2026-10-06 prod copy is skipped, not overwritten).
 *
 * Every number and product claim here comes from a published drtakaful.com
 * page (named in key_facts). No promotions, deadlines or client stories are
 * invented. Copy follows the drtakaful style: Caruman not premium, everyday
 * English where Malaysians use it, few dashes.
 */

return [

    // ── 1 ───────────────────────────────────────────────────────────────
    1 => [
        'match_title'  => 'WhatsApp Warm Outreach untuk Wanita Bekerjaya',
        'title'        => 'Wanita Bekerjaya: "Company Dah Cover, Cukup Ke?"',
        'description'  => 'Bila guna: kenalan wanita yang bekerja makan gaji (kawan lama, rakan sekerja, ahli group). Mulakan dengan satu soalan pasal cover company, bukan dengan produk. Medical card diposisikan sebagai gap filler, bukan pengganti.',
        'category'     => 'prospecting',
        'channel'      => 'whatsapp',
        'audience'     => 'strangers',
        'product_line' => 'medical',
        'angle_cold' => <<<'TXT'
Dia belum pernah borak takaful dengan you. Jangan buka dengan "ada plan takaful?", itu terus rasa kena jual. Buka dengan satu soalan yang dia sendiri tak pasti jawapannya: apa jadi pada cover company bila berhenti kerja. Minta izin dulu sebelum share apa-apa.

Mesej: "Hi [Nama], lama tak borak! Nak tanya satu soalan random, boleh? Medical card company you tu, kalau satu hari you tukar kerja atau berhenti, cover dia ikut you ke tamat terus? Ramai kawan I tak pernah check, tu yang I tanya 😊"
TXT,
        'angle_warm' => <<<'TXT'
Dia dah balas atau pernah tanya pasal medical card. Sekarang buat dia semak polisi company sendiri, bukan dengar you cerita. Bagi 3 benda nak check, dan offer tolong baca.

Mesej: "Kalau you free nanti, cuba tgk 3 benda dalam polisi company you: had tahunan berapa, had bilik semalam berapa, dan cover tamat bila berhenti kerja ke tak. Snap je page tu send kat I, I tolong baca dan bagitau mana yang ada gap. Free je, takde commitment 🙂"
TXT,
        'angle_hot' => <<<'TXT'
Dia dah minta harga. Bagi harga contoh yang betul, terangkan apa yang TAK dicover dulu (exclusion, waiting period), dan ajak isi borang isytihar kesihatan sama-sama supaya GL tak bermasalah nanti.

Mesej: "Untuk umur 30, caruman bermula dari RM169 sebulan untuk perempuan. Had tahunan RM1.5 juta++, takde had seumur hidup, dan tahun tak claim ada kredit Health Wallet. Sebelum you decide, I nak terangkan dulu apa yang TAK dicover (exclusion dan waiting period), supaya takde surprise masa emergency. Bila sesuai kita call 10 minit?"
TXT,
        'key_facts' => <<<'TXT'
• Cover company selalunya tamat bila berhenti atau tukar kerja, dan biasanya tamat bila bersara (kecuali pesara kerajaan). [/go/mc-quote]
• Had tahunan cover company selalunya puluhan ribu. Medical card sendiri: RM1.5 juta++ had tahunan, tiada had seumur hidup. [/go/mc-quote]
• Banyak polisi company hadkan bilik RM150 hingga RM200 semalam, bilik paling murah di hospital swasta bandar selalunya RM250 hingga RM300. Beza tu bayar sendiri. [/go/company-mc]
• Tahun tak claim: kredit RM1,000 hingga RM2,500 masuk Health Wallet untuk kos kesihatan. [/go/mc-quote]
• Contoh caruman umur 30: perempuan dari RM169, lelaki dari RM189 sebulan. [/go/mc-quote] (sahkan produk mana sebelum quote)
• "Kenapa sekarang": ambil masa masih sihat dan masih ada cover company. Tunggu sampai berhenti kerja atau dah sakit, permohonan baru mungkin datang dengan exclusion atau caruman lebih tinggi.
Link untuk send: drtakaful.com/go/company-mc (baca dulu), drtakaful.com/go/mc-quote (semak caruman)
TXT,
        'steps' => [
            ['title' => 'Buka dengan satu soalan, bukan produk', 'timing_note' => 'Hari 1, waktu rehat (12:30 hingga 2pm) atau lepas kerja (8 hingga 10pm)',
             'script' => 'Hi [Nama], lama tak borak! Nak tanya satu soalan random, boleh? Medical card company you tu, kalau satu hari you tukar kerja atau berhenti, cover dia ikut you ke tamat terus? Ramai kawan I tak pernah check, tu yang I tanya 😊',
             'branch_yes' => 'Dia jawab (tahu atau tak tahu): terus ke Step 2.',
             'branch_no' => 'Takde balasan 3 hari: hantar sekali je "Takpe kalau busy 🙂 soalan tu je, takde apa nak jual." Lepas tu stop, tandakan Cold dalam leads.'],
            ['title' => 'Biar dia semak sendiri', 'timing_note' => 'Dalam perbualan yang sama',
             'script' => 'Haa ramai tak sure jugak. Selalunya cover company tamat bila berhenti kerja, dan had tahunan dia puluhan ribu je. Kalau you free, cuba tgk 3 benda: had tahunan, had bilik semalam, dan tamat bila berhenti kerja ke tak. Snap send kat I, I tolong baca. Free je.',
             'branch_yes' => 'Dia send polisi: baca betul-betul, jawab dengan 1 atau 2 gap yang paling besar je (Step 3).',
             'branch_no' => '"Nanti la": balas "Boleh, bila-bila ready send je" dan set next contact 2 minggu.'],
            ['title' => 'Tunjuk gap, satu je', 'timing_note' => 'Lepas baca polisi dia',
             'script' => 'Dah tgk. Polisi company you ni ok untuk admission biasa, tapi had tahunan [RMxx,xxx] dan bilik [RMxxx] semalam. Kalau kena pembedahan besar atau rawatan kanser di hospital swasta, bil boleh jauh lebih tinggi dari tu. Medical card sendiri ni gap filler, bukan pengganti. Nak I kira caruman ikut umur you?',
             'branch_yes' => 'Terus ke Step 4.',
             'branch_no' => 'Hormat. "Ok noted, at least you dah tahu gap dia. Kalau satu hari nak tutup gap tu, I ada." Set next contact 1 bulan.'],
            ['title' => 'Harga contoh + apa yang TAK dicover', 'timing_note' => 'Dalam perbualan yang sama',
             'script' => 'Untuk umur [umur], caruman bermula dari RM[xxx] sebulan. Had tahunan RM1.5 juta++, takde had seumur hidup, dan tahun tak claim ada kredit Health Wallet RM1,000 hingga RM2,500. Sebelum you decide, I nak terangkan dulu apa yang TAK dicover: exclusion dan waiting period. Bila sesuai kita call 10 minit?',
             'branch_yes' => 'Set masa call. Tukar lead jadi Hot.',
             'branch_no' => 'Hantar link drtakaful.com/go/mc-quote supaya dia baca sendiri. Follow up 5 hari kemudian dengan 1 soalan: "Ada bahagian yang tak clear?"'],
            ['title' => 'Tutup dengan baik', 'timing_note' => 'Kalau masih senyap selepas Step 4',
             'script' => 'Hi [Nama], I takkan kacau lagi pasal ni 🙂 Cuma satu je nak pesan: ambil masa masih sihat dan masih ada cover company, sebab bila dah sakit, permohonan baru boleh datang dengan exclusion. Bila-bila you nak semak, WhatsApp je.',
             'branch_yes' => 'Dia balas: sambung ikut Step 4.',
             'branch_no' => 'Stop. Set next contact 3 bulan, atau bila ada life event (kahwin, tukar kerja, anak).'],
        ],
    ],

    // ── 2 ───────────────────────────────────────────────────────────────
    2 => [
        'match_title'  => 'Matcha Strawberry & Your Future: Takaful for Gen Z',
        'title'        => 'Matcha Strawberry vs Hibah RM45',
        'description'  => 'Bila guna: post IG atau story untuk umur 20an hingga awal 30an. Bandingkan belanja minuman seminggu dengan caruman hibah RM350,000 yang bermula RM45 sebulan. Ringan, relatable, dan nombor dia betul.',
        'category'     => 'content',
        'channel'      => 'instagram',
        'audience'     => 'strangers',
        'product_line' => 'hibah',
        'content' => <<<'TXT'
Siapa sini mesti beli matcha strawberry seminggu sekali? 🙋‍♀️ Sama.

Jom kira sikit. Satu cup dalam RM15. 3 kali seminggu, sebulan dah dekat RM180.

Sekarang bandingkan dengan ni: hibah takaful coverage RM350,000 boleh bermula sekitar RM45 sebulan, ikut umur dan tempoh. Lebih kurang 3 cup je.

Bukan suruh berhenti minum matcha 😅 Cuma nak share, hibah ni bukan untuk orang kaya je. Tak perlu ada rumah, tanah atau bisnes. Yang perlu cuma ada orang yang bergantung pada kita: mak ayah, pasangan, adik-adik.

Dan bila kita tiada, duit tu sampai terus kepada orang yang kita namakan, tak perlu tunggu urusan pusaka selesai dulu.

Nak tahu caruman ikut umur you? Komen "HIBAH" atau DM, saya Hana, agent AIA, saya kira untuk you 🙂

#hibahtakaful #adulting #duitkita #matchastrawberry
TXT,
        'angle_cold' => <<<'TXT'
Post ni untuk orang yang tak kenal you. Jangan letak link dalam caption, biar mereka komen dulu (komen = signal Warm). Story: letak poll "Seminggu berapa cup? 1 / 3 / 5+", kemudian story kedua reveal RM45.

Mesej: "Seminggu berapa cup matcha you? Kalau 3 cup, tu dah lebih kurang sama dengan caruman hibah RM350,000 sebulan 👀 Komen HIBAH kalau nak tahu kira-kira dia."
TXT,
        'angle_warm' => <<<'TXT'
Untuk yang komen "HIBAH" atau undi poll. Balas komen di public (orang lain nampak you responsive), kemudian DM. Tanya satu soalan untuk faham siapa yang bergantung pada dia.

Mesej: "Hi! Terima kasih komen 🙂 Nak tanya sikit supaya I bagi angka yang tepat: umur you berapa, dan siapa yang paling you nak jaga kalau apa-apa jadi? Mak ayah, pasangan, atau adik-adik?"
TXT,
        'angle_hot' => <<<'TXT'
Dia dah bagi umur dan tanya harga. Bagi angka, terangkan siapa terima (penama), dan apa yang TAK dicover dulu. Link page penuh untuk dia baca sendiri.

Mesej: "Untuk umur [umur], coverage RM350,000 caruman dia sekitar RM[xx] sebulan untuk tempoh [x] tahun. Duit tu terus kepada penama yang you namakan. Sebelum decide, I terangkan dulu syarat dan apa yang boleh buat tuntutan ditolak, supaya takde surprise untuk family nanti. Boleh baca dulu sini: drtakaful.com/go/hibah-quote"
TXT,
        'key_facts' => <<<'TXT'
• Hibah RM350,000 boleh bermula sekitar RM45 sebulan, ikut umur dan tempoh yang dipilih. [/go/hibah-quote, /go/rm45]
• Bayar terus kepada penama yang dinamakan, tanpa tunggu pembahagian pusaka. Faraid tetap berjalan untuk harta lain. [/go/hibah-quote]
• Urusan pentadbiran harta boleh ambil 6 hingga 12 bulan. Dalam tempoh tu bil, sewa dan yuran tetap kena bayar. [/go/hibah-quote]
• "Hibah untuk orang kaya je" = salah faham paling mahal. Tak perlu rumah, tanah atau bisnes, cuma orang yang bergantung pada kita. [/go/hibah-quote]
• Caruman dikira ikut umur dan kesihatan semasa mohon. Makin lama tunggu, makin tinggi. [/go/hibah-quote]
Link: drtakaful.com/go/hibah-quote (semak caruman), drtakaful.com/go/fresh-grad (untuk gaji pertama)
TXT,
    ],

    // ── 4 ───────────────────────────────────────────────────────────────
    4 => [
        'match_title'  => 'WhatsApp Follow-Up untuk Warm Leads Takaful',
        'title'        => 'Follow-Up Lead Senyap: Cari Sebab Sebenar',
        'description'  => 'Bila guna: lead yang pernah tanya atau dah dapat quotation, kemudian senyap 3 hari atau lebih. Bukan kejar, tapi cari apa yang buat dia ragu (caruman, kesihatan, atau masa) dan jawab yang itu je.',
        'category'     => 'follow_up',
        'channel'      => 'whatsapp',
        'audience'     => 'warm_leads',
        'product_line' => 'general',
        'angle_warm' => <<<'TXT'
Dia pernah tanya tapi belum minta harga. Jangan tanya "dah fikir?", tu tekanan. Bagi satu benda berguna yang dia boleh guna walaupun tak beli dengan you.

Mesej: "Hi [Nama] 🙂 Haritu you ada tanya pasal [medical card / hibah]. I teringat satu benda yang ramai tak tahu: [satu fakta dari Facts to use]. Kalau berguna, simpan je. Kalau ada soalan, I ada."
TXT,
        'angle_hot' => <<<'TXT'
Dia dah dapat quotation tapi senyap. Biasanya ada satu sebab yang dia segan nak sebut. Bagi dia 3 pilihan jawapan, senang dia tekan satu.

Mesej: "Hi [Nama], quotation haritu ok ke? Biasanya kalau orang senyap lepas dapat harga, sebab dia salah satu ni: 1) caruman rasa tinggi, 2) risau pasal sejarah kesihatan, 3) belum masa lagi. Reply nombor je pun ok, I adjust ikut tu 🙂"
TXT,
        'key_facts' => <<<'TXT'
Jawapan untuk 3 sebab biasa:
1) Caruman tinggi: hibah RM350,000 boleh bermula sekitar RM45 sebulan, mula kecil dulu dan naikkan bila pendapatan naik. [/go/hibah-quote] Medical card: tahun tak claim, kredit RM1,000 hingga RM2,500 masuk Health Wallet, caruman tak "hangus" macam tu je. [/go/mc-quote]
2) Sejarah kesihatan: tak semestinya ditolak. Permohonan boleh diterima biasa, dengan exclusion untuk penyakit tu sahaja, atau dengan caruman tambahan. Isytihar dengan jujur, sebab penyakit tak diisytiharkan antara sebab utama GL kena decline. [/go/mc-quote, /go/gl-decline]
3) Belum masa: caruman dikira ikut umur dan kesihatan semasa mohon. Tunggu sampai kesihatan berubah, permohonan boleh datang dengan syarat tambahan. [/go/hibah-quote]
Jangan guna: promosi atau "harga naik hujung bulan" yang tak wujud. Urgency yang betul cuma umur dan kesihatan.
TXT,
        'steps' => [
            ['title' => 'Check-in dengan value, bukan "dah fikir?"', 'timing_note' => '3 hari selepas senyap',
             'script' => 'Hi [Nama] 🙂 Haritu you ada tanya pasal [produk]. I teringat satu benda yang ramai tak tahu: [satu fakta dari Facts to use, ikut produk]. Kalau berguna, simpan je. Kalau ada soalan, I ada.',
             'branch_yes' => 'Dia balas dengan soalan: jawab soalan tu je, kemudian tanya "Nak I kira caruman ikut umur you?"',
             'branch_no' => 'Takde balasan: tunggu 3 hari, ke Step 2.'],
            ['title' => 'Bagi 3 pilihan sebab', 'timing_note' => '3 hari selepas Step 1',
             'script' => 'Hi [Nama], nak tanya terus terang je 🙂 Biasanya orang senyap sebab salah satu ni: 1) caruman rasa tinggi, 2) risau pasal sejarah kesihatan, 3) belum masa lagi. Reply nombor je pun ok, I adjust ikut tu.',
             'branch_yes' => 'Dia reply nombor: ke Step 3 dengan jawapan untuk nombor tu.',
             'branch_no' => 'Takde balasan: tunggu 5 hari, ke Step 4.'],
            ['title' => 'Jawab sebab yang dia pilih, itu je', 'timing_note' => 'Dalam perbualan yang sama',
             'script' => '1) Caruman: "Boleh mula kecil dulu. Contoh hibah, RM350k boleh bermula sekitar RM45 sebulan. Medical card pulak, tahun tak claim ada kredit Health Wallet, so caruman tak hangus macam tu je. Nak I buat pilihan ikut bajet you?"
2) Kesihatan: "Tak semestinya reject. Boleh diterima biasa, dengan exclusion untuk penyakit tu je, atau caruman tambahan. Cara nak tahu, isytihar dengan jujur. I boleh semak borang sama-sama."
3) Masa: "Faham. Cuma caruman dikira ikut umur dan kesihatan masa mohon. Bila you rasa sesuai?"',
             'branch_yes' => 'Dia setuju tengok pilihan atau isi borang: set call, tukar lead jadi Hot.',
             'branch_no' => 'Dia bagi bulan atau tarikh: set next contact pada tarikh tu dan stop sekarang.'],
            ['title' => 'Mesej terakhir, tanpa rasa bersalah', 'timing_note' => '5 hari selepas Step 2, kalau masih senyap',
             'script' => 'Hi [Nama], I simpan quotation you ya, takkan kacau lagi pasal ni 🙂 Bila-bila you ready atau ada soalan, WhatsApp je terus. Semoga sihat selalu.',
             'branch_yes' => 'Dia balas: sambung ikut Step 3.',
             'branch_no' => 'Stop. Set next contact 2 hingga 3 bulan. Tukar lead jadi Warm kalau dia Hot.'],
            ['title' => 'Bila tiba tarikh next contact', 'timing_note' => '2 hingga 3 bulan kemudian, atau ada life event',
             'script' => 'Hi [Nama], lama tak dengar khabar! Haritu you ada tanya pasal [produk]. Ada apa-apa berubah? Kalau nak I kira semula ikut umur sekarang, bagitau je 🙂',
             'branch_yes' => 'Mula semula dari Step 3.',
             'branch_no' => 'Tandakan lead sebagai tidak aktif. Cuba lagi bila ada life event.'],
        ],
    ],

    // ── 6 ───────────────────────────────────────────────────────────────
    6 => [
        'match_title'  => 'Objection Handling for Existing Policyholders - Family Protection Upgrade',
        'title'        => '"Saya Dah Ada Insurans": Semakan Polisi Percuma',
        'description'  => 'Bila guna: orang yang cakap dah ada polisi (company, PERKESO, atau medical card sendiri), dan klien sedia ada yang hanya ada Medical Card. Jangan lawan polisi dia. Tawarkan semakan, cari satu gap, dan isi gap tu je, selalunya pendapatan bila sakit kritikal.',
        'category'     => 'objection_handling',
        'channel'      => 'whatsapp',
        'audience'     => 'general',
        'product_line' => 'critical_illness',
        'angle_cold' => <<<'TXT'
Orang yang cakap "dah ada" masa you baru kenal. Puji, jangan bantah. Tawarkan semakan percuma sebagai "second opinion", bukan pitch.

Mesej: "Bagus la dah ada, ramai yang belum 👍 Kalau satu hari nak second opinion, I boleh semak polisi tu free, takde jual apa-apa. Selalunya I check 3 benda je: had tahunan, cover tamat bila berhenti kerja ke tak, dan ada cover gaji kalau sakit kritikal ke tak."
TXT,
        'angle_warm' => <<<'TXT'
Klien sedia ada (Medical Card sahaja) atau lead yang dah share polisi. Medical card bayar hospital, tapi bukan ganti gaji. Itu gap paling biasa.

Mesej: "Hi [Nama], medical card you dah cover bil hospital 👍 Satu benda yang ramai terlepas pandang: bila kena sakit kritikal macam kanser atau strok, doktor boleh minta rehat 12 hingga 24 bulan. Bil hospital settle, tapi siapa bayar komitmen bulanan masa tu? Nak I kira berapa cover gaji yang sesuai dengan you?"
TXT,
        'angle_hot' => <<<'TXT'
Dia setuju ada gap dan nak tahu harga. Kira guna formula gaji x 24 bulan, dan sebut kalau ada pilihan bayar dari peringkat awal.

Mesej: "Cara kira yang I guna: gaji sebulan x 24 bulan, supaya you ada 2 tahun untuk fokus pulih tanpa stress kewangan. Gaji RM[x] = cover sekitar RM[x x 24]. Ada pilihan modul peringkat awal, jadi tak perlu tunggu stage lanjut baru boleh claim. Nak I sediakan 2 pilihan caruman?"
TXT,
        'key_facts' => <<<'TXT'
• Medical card bayar bil hospital, tapi ia bukan ganti gaji. [/go/mc-quote]
• Sakit kritikal (kanser, strok): doktor boleh minta rehat 12 hingga 24 bulan. [/go/mc-ci, /go/kos-rawatan]
• Formula: cover CI = gaji bulanan x 24 bulan, 2 tahun untuk pulih. [/go/mc-ci]
• PERKESO cover kemalangan dan penyakit berkaitan kerja sahaja. Denggi, strok, kanser biasa bukan berkaitan kerja, jadi tak dicover. [/go/perkeso-mc, /go/socso-ci]
• Pencen ilat SOCSO lebih kurang 24% purata gaji. Gaji RM5,000 = sekitar RM1,200 sebulan. [/go/socso-ci]
• SOCSO berhenti bila berhenti kerja. Freelancer dan business owner tak dicover secara automatik. [/go/socso-ci]
• CI Flex: pelan asas bayar peringkat lanjut. Tambah modul Peringkat Awal untuk pampasan dari peringkat awal dan pertengahan. [/go/ci-flex]
• Cover company: tamat bila berhenti atau bersara, had selalunya puluhan ribu. [/go/company-mc]
Link: drtakaful.com/go/mc-ci, drtakaful.com/go/socso-ci, drtakaful.com/go/company-mc
TXT,
        'steps' => [
            ['title' => 'Hormat polisi dia, tawar second opinion', 'timing_note' => 'Hari 1',
             'script' => 'Bagus la dah ada, ramai yang belum 👍 Kalau satu hari nak second opinion, I boleh semak polisi tu free. Selalunya I check 3 benda je: had tahunan, cover tamat bila berhenti kerja ke tak, dan ada cover gaji kalau sakit kritikal ke tak.',
             'branch_yes' => 'Dia setuju: ke Step 2.',
             'branch_no' => '"Takpe dah cukup": balas "Noted, simpan nombor I kalau satu hari nak semak 🙂". Stop, set next contact 3 bulan.'],
            ['title' => 'Kenal pasti jenis polisi', 'timing_note' => 'Dalam perbualan yang sama',
             'script' => 'Polisi tu dari company, PERKESO, atau you ambil sendiri? Dan dalam tu ada medical card je, atau ada pelan sakit kritikal sekali? Kalau senang, snap page manfaat dia send kat I.',
             'branch_yes' => 'Dia jelaskan: ke Step 3, pilih gap yang betul.',
             'branch_no' => 'Dia tak pasti: "Takpe, nanti bila jumpa dokumen tu send je. Biasanya page pertama dah cukup."'],
            ['title' => 'Tunjuk SATU gap', 'timing_note' => 'Lepas tengok polisi',
             'script' => 'Company: "Cover ni tamat bila you berhenti kerja atau bersara, dan had dia [RMxx,xxx]."
PERKESO: "PERKESO cover kemalangan dan penyakit berkaitan kerja je. Kalau denggi atau strok, ia tak cover."
Medical card sahaja: "Medical card settle bil hospital. Tapi kalau kena kanser atau strok dan perlu rehat 1 hingga 2 tahun, siapa bayar komitmen bulanan?"',
             'branch_yes' => 'Dia nampak gap: ke Step 4.',
             'branch_no' => 'Dia rasa cukup: "Faham, at least you dah tahu gap dia." Hantar link berkaitan dan stop.'],
            ['title' => 'Isi gap tu je, bukan ganti semua', 'timing_note' => 'Dalam perbualan yang sama',
             'script' => 'I tak suruh you buang polisi sedia ada. Kita isi gap tu je. Cara kira yang I guna: gaji sebulan x 24 bulan, supaya ada 2 tahun untuk pulih tanpa stress kewangan. Nak I sediakan 2 pilihan caruman, satu asas dan satu dengan modul peringkat awal?',
             'branch_yes' => 'Sediakan 2 pilihan. Terangkan apa yang TAK dicover dulu sebelum harga.',
             'branch_no' => '"Mahal": tawar cover lebih kecil dulu (gaji x 12) dan naikkan kemudian.'],
            ['title' => 'Bantu dia buat keputusan', 'timing_note' => 'Selepas hantar pilihan',
             'script' => 'Antara 2 pilihan tu, mana yang lebih selesa dengan bajet you? Kalau dah ok, kita isi borang isytihar kesihatan sama-sama supaya takde butiran tertinggal yang boleh jadi masalah masa claim nanti.',
             'branch_yes' => 'Proses permohonan.',
             'branch_no' => '"Nak fikir dulu": "Boleh. Bila sesuai I follow up?" Set next contact pada tarikh yang dia pilih.'],
        ],
    ],

    // ── 8 ───────────────────────────────────────────────────────────────
    8 => [
        'match_title'  => 'The C-Word Conversation',
        'title'        => 'The C-Word Conversation (Kanser Payudara)',
        'description'  => 'Bila guna: Oktober (bulan kesedaran kanser payudara) atau bila topik kanser trending. Pendekatan empati, bukan takut. Cakap pasal pengesanan awal dan masa pemulihan, kemudian perlindungan sakit kritikal sebagai perancangan yang bertanggungjawab.',
        'category'     => 'content',
        'channel'      => 'facebook',
        'audience'     => 'general',
        'product_line' => 'critical_illness',
        'content' => <<<'TXT'
Oktober ni bulan kesedaran kanser payudara 🎗️

Saya nak cakap pasal satu benda yang ramai tak suka dengar, tapi penting.

Kanser payudara antara kanser paling biasa dalam kalangan wanita di Malaysia. Berita baiknya, bila dikesan awal, peluang untuk pulih jauh lebih baik. Jadi perkara pertama, sebelum apa-apa pasal takaful: buat pemeriksaan sendiri, dan pergi saringan bila doktor sarankan. Itu yang paling penting.

Benda kedua yang jarang orang cerita: masa pemulihan. Doktor boleh minta rehat 12 hingga 24 bulan. Medical card boleh bantu bil hospital, tapi ia bukan ganti gaji. Siapa bayar sewa, kereta, yuran anak masa tu?

Sebab tu saya selalu cadangkan pelan sakit kritikal bersama medical card. Sesetengah pelan sekarang boleh bayar dari peringkat awal lagi, tak perlu tunggu stage lanjut.

Bukan untuk takutkan sesiapa. Cuma bila kita dah rancang, kita boleh fokus pada satu benda je masa tu: sembuh.

Tag kawan perempuan yang you sayang, ingatkan dia buat pemeriksaan bulan ni 💗

Saya Hana, agent AIA. Kalau nak semak cover sedia ada, DM je, takde paksaan.
TXT,
        'angle_cold' => <<<'TXT'
Post untuk umum. Kesihatan dulu, produk kemudian. Jangan guna gambar pesakit sebenar atau kisah orang lain tanpa izin. CTA utama ialah "tag kawan", bukan "DM saya". Engagement datang dulu, lead kemudian.

Mesej: "Oktober ni bulan kesedaran kanser payudara 🎗️ Satu benda je saya nak minta: tag seorang kawan perempuan dan ingatkan dia buat pemeriksaan bulan ni 💗"
TXT,
        'angle_warm' => <<<'TXT'
Untuk yang like, komen, atau tag kawan. DM dengan rasa prihatin, bukan jualan. Tanya pasal cover sedia ada, kemudian tunjuk gap gaji.

Mesej: "Hi [Nama], terima kasih share post tu 💗 Nak tanya sikit, you dah ada medical card? Ramai tak perasan, medical card bayar bil hospital tapi bukan ganti gaji masa rehat. Kalau nak, I boleh semak cover you, free je."
TXT,
        'angle_hot' => <<<'TXT'
Dia nak tahu harga pelan CI. Kira gaji x 24 bulan, terangkan beza bayar peringkat lanjut sahaja vs dengan modul peringkat awal, dan apa yang TAK dicover.

Mesej: "Untuk you, cover yang I cadangkan sekitar RM[gaji x 24]. Ada 2 pilihan: pelan asas yang bayar peringkat lanjut, atau tambah modul Peringkat Awal supaya boleh claim dari peringkat awal lagi. I terangkan dulu syarat dan apa yang tak dicover, kemudian you pilih ikut bajet 🙂"
TXT,
        'key_facts' => <<<'TXT'
• Medical card bayar bil hospital tapi bukan ganti gaji. [/go/mc-quote]
• Sakit kritikal: doktor boleh minta rehat 12 hingga 24 bulan. Cover cadangan = gaji x 24 bulan. [/go/mc-ci]
• CI Flex: 3 peringkat (awal, pertengahan, lanjut). Pelan asas bayar peringkat lanjut. Tambah modul Peringkat Awal untuk pampasan dari peringkat awal. Tuntutan awal ditolak dari baki pelan. [/go/ci-flex]
• Pastikan statistik dihedge ("antara kanser paling biasa"), jangan beri angka tanpa sumber.
• Jangan guna gambar atau kisah pesakit sebenar tanpa izin. Kisah dari berita = sebut "kisah dari berita", bukan "klien saya".
Link: drtakaful.com/go/ci-flex, drtakaful.com/go/mc-ci
TXT,
    ],

    // ── 9 ───────────────────────────────────────────────────────────────
    9 => [
        'match_title'  => 'Keyman Takaful untuk Boss Kecil',
        'title'        => 'Boss Kecil: Kedai Tetap Jalan Bila Boss Sakit',
        'description'  => 'Bila guna: pemilik bisnes kecil (kedai runcit, hardware, bengkel, online seller). Takde majikan bermaksud takde cover automatik. Fokus pada pendapatan bila boss sakit (pelan sakit kritikal), dan hibah silang kalau ada rakan kongsi.',
        'category'     => 'prospecting',
        'channel'      => 'facebook',
        'audience'     => 'strangers',
        'product_line' => 'critical_illness',
        'content' => <<<'TXT'
Untuk boss-boss kedai runcit, hardware, bengkel dan online seller 👋

Satu soalan je: kalau esok you kena masuk hospital 3 bulan, kedai siapa jaga? Duit stok, gaji pekerja, sewa kedai, siapa bayar?

Bila kita kerja sendiri:
✘ Takde boss, takde cover company
✘ EPF tak automatik
✘ SOCSO tak automatik, kena daftar sendiri

Bila boss sakit, bisnes selalunya sakit sekali.

Pelan sakit kritikal bayar lump sum bila didiagnos penyakit kritikal. Duit tu boleh guna untuk apa je: upah orang jaga kedai, bayar sewa, atau sara family masa you pulih.

Dan kalau bisnes you ada rakan kongsi, ada satu lagi soalan penting: kalau partner you meninggal, ada tak duit tunai untuk beli saham dia dari waris dia? Tu fungsi hibah silang.

Saya Hana, agent AIA. Nak saya kira cover yang sesuai dengan bisnes you? DM "BOSS" 🙂
TXT,
        'angle_cold' => <<<'TXT'
Pemilik bisnes kecil yang tak kenal you. Buka dengan soalan operasi, bukan takaful. Mereka fikir pasal kedai, bukan polisi.

Mesej: "Boss, satu soalan je: kalau esok boss kena masuk hospital 3 bulan, kedai siapa jaga? Sewa dengan gaji pekerja macam mana? Ramai boss tak pernah fikir sebab takde cover company macam orang makan gaji."
TXT,
        'angle_warm' => <<<'TXT'
Dia jawab soalan tu atau DM "BOSS". Tanya 2 benda: solo atau ada partner, dan berapa kos operasi sebulan. Jawapan tu tentukan produk: solo = CI (+ hibah untuk family), partner = hibah silang.

Mesej: "Terima kasih boss 🙂 2 soalan je supaya I bagi cadangan yang betul: 1) bisnes ni solo atau ada partner? 2) Kalau kedai tutup sebulan, lebih kurang berapa kos yang tetap kena bayar (sewa, gaji, hutang)?"
TXT,
        'angle_hot' => <<<'TXT'
Dah tahu struktur bisnes dan kos. Solo: kira cover CI ikut kos bulanan x 24. Partner: terangkan hibah silang, setiap partner ambil pelan atas hayat partner lain, nilai = nilai saham.

Mesej: "Untuk bisnes boss, I cadangkan cover sekitar RM[kos bulanan x 24], supaya ada 2 tahun duit operasi kalau boss tak boleh kerja. Kalau ada partner, kita tambah hibah silang supaya bisnes tak jatuh ke tangan orang lain. Bila boss free, I terangkan 10 minit?"
TXT,
        'key_facts' => <<<'TXT'
• Kerja sendiri: takde caruman EPF automatik, SOCSO tak automatik (kena daftar sendiri). [/go/hibah-creator]
• SOCSO tak cover freelancer dan business owner secara automatik. Skim sukarela ada, tapi coverage terhad. [/go/socso-ci]
• Sakit kritikal: doktor boleh minta rehat 12 hingga 24 bulan. Cover = kos atau gaji bulanan x 24. [/go/mc-ci]
• Hibah silang: setiap rakan kongsi ambil pelan atas hayat rakan kongsi lain, digabung dengan perjanjian jual beli saham. Bila seorang meninggal, yang hidup terima tunai untuk beli saham daripada waris, waris dapat tunai, bisnes terus berjalan. [/go/hibah-bisnes]
• Solo (dropship, affiliate, kedai sendiri tanpa partner formal): hibah silang tak relevan, tapi hibah biasa masih penting untuk family. [/go/hibah-bisnes]
Nota: produk "keyman takaful" tidak ada di drtakaful.com. Sahkan dengan Hana sebelum sebut nama produk itu.
Link: drtakaful.com/go/hibah-bisnes, drtakaful.com/go/hibah-creator
TXT,
    ],

    // ── 10 ──────────────────────────────────────────────────────────────
    10 => [
        'match_title'  => 'Instagram DM Value-First Engagement for Takaful',
        'title'        => 'Komen ke DM: Jawab Risau Sebenar Dulu',
        'description'  => 'Bila guna: orang yang tanya atau komen pasal medical card, hibah atau takaful di IG, Threads atau FB. Jawab kerisauan sebenar dia di public dengan satu fakta yang agent lain tak bagi, kemudian ajak DM. Selalu sign off "saya Hana, agent AIA".',
        'category'     => 'prospecting',
        'channel'      => 'instagram',
        'audience'     => 'strangers',
        'product_line' => 'general',
        'content' => <<<'TXT'
LANGKAH 1: Pilih post yang betul
• Baru (jam atau hari, bukan bulan lepas), soalan sebenar dari orang biasa.
• Skip post agent lain. Check profile: kalau bio ada "agent", "advisor", atau link insurans, dia bukan prospek.
• Check dulu you belum pernah reply post tu.

LANGKAH 2: Reply di public (pendek, satu fakta)
Formula: namakan risau sebenar dia → satu fakta spesifik → ajak DM → sign off.

Contoh (anak kerap demam):
"Untuk baby, elok tgk bukan limit tinggi je, tapi outpatient/klinik coverage, sebab baby lagi kerap demam, bukan admission. Boleh DM kalau nak compare plan 🙂, saya Hana, agent AIA"

Contoh (ada sejarah sakit):
"Ada sejarah sakit tak semestinya reject kak. Boleh diterima biasa, dengan exclusion untuk penyakit tu je, atau caruman tambahan. Yang penting isytihar dengan jujur, sebab tu yang selalu buat GL decline nanti. Boleh DM kalau nak semak 🙂, saya Hana, agent AIA"

LANGKAH 3: DM (bila dia reply atau DM)
"Hi! Terima kasih reply 🙂 Nak tanya sikit supaya I bagi jawapan tepat: umur berapa, dan apa yang paling risau, bil hospital besar, atau cover company tak cukup?"

LANGKAH 4: Bagi link yang sepadan
Medical card: drtakaful.com/go/mc-quote
Hibah: drtakaful.com/go/hibah-quote
GL decline: drtakaful.com/go/gl-decline

Pantang: jangan sebut harga dalam reply public pertama, jangan copy paste reply yang sama ke banyak post, jangan janji "GL mesti lulus".
TXT,
        'angle_cold' => <<<'TXT'
Orang yang tanya soalan di public tapi tak kenal you. Reply di public dulu, bukan DM terus (DM terus dari stranger rasa macam spam). Satu fakta je, kemudian sign off.

Mesej: "[Namakan risau dia]. [Satu fakta dari Facts to use]. Boleh DM kalau nak semak ikut situasi you 🙂, saya Hana, agent AIA"
TXT,
        'angle_warm' => <<<'TXT'
Dia reply komen you atau DM. Tanya 2 soalan je (umur + risau utama), kemudian hantar link page yang sepadan supaya dia baca sendiri.

Mesej: "Hi! Terima kasih reply 🙂 Nak tanya sikit supaya I bagi jawapan tepat: umur berapa, dan apa yang paling risau, bil hospital besar, atau cover company tak cukup?"
TXT,
        'angle_hot' => <<<'TXT'
Dia minta harga dalam DM. Pindah ke WhatsApp, bagi harga contoh, dan terangkan apa yang TAK dicover dulu.

Mesej: "Boleh, I kira untuk you 🙂 Senang kita sambung di WhatsApp, I boleh send quotation terus: wa.me/60132522587. Before harga, I akan terangkan dulu exclusion dan waiting period, supaya takde surprise nanti."
TXT,
        'key_facts' => <<<'TXT'
• Medical card untuk bil besar (wad, pembedahan, kanser), bukan klinik RM50 untuk selesema. Ramai kecewa sebab salah faham. [/go/mc-quote]
• Sejarah sakit: diterima biasa, dengan exclusion, atau caruman tambahan. Tak semestinya reject. [/go/mc-quote]
• GL decline tak sama dengan tuntutan ditolak. Boleh Pay & Claim. Jangan percaya agent yang janji "GL mesti lulus". [/go/gl-decline]
• Waiting period: 30 hari untuk kebanyakan penyakit, 120 hari untuk senarai penyakit tertentu. 2 tahun pertama: syarikat boleh semak non-disclosure. [/go/mc-waiting]
• Satu kali masuk wad swasta (contoh appendix) boleh cecah RM10k hingga RM15k. [/go/mc-quote]
• Panel hospital: GL untuk hospital tertentu case-by-case ikut panel. Sebut hanya bila dah sahkan.
• Sign off wajib: "saya Hana, agent AIA".
TXT,
    ],

    // ── 11 ──────────────────────────────────────────────────────────────
    11 => [
        'match_title'  => 'Post untuk Makcik Pakcik: Kenapa Takaful Penting untuk Kita Semua',
        'title'        => 'Post Makcik Pakcik: "Dah 50an, Masih Boleh Ke?"',
        'description'  => 'Bila guna: FB untuk audiens 50 tahun ke atas, dan anak-anak mereka. Jawab soalan yang mereka segan tanya ("dah tua, ada ke yang nak terima?") dengan jujur: boleh, tapi caruman lebih tinggi dan mungkin ada syarat. Untuk anak-anak, angle hibah untuk ibu bapa.',
        'category'     => 'content',
        'channel'      => 'facebook',
        'audience'     => 'strangers',
        'product_line' => 'general',
        'content' => <<<'TXT'
Assalamualaikum makcik pakcik 🙂

Selalu saya dapat soalan ni: "Dah 50 lebih, ada ke syarikat nak terima?"

Jawapan jujur saya: boleh. Untuk sesetengah pelan, permohonan diterima sehingga umur 70 tahun.

Tapi saya tak nak bagi gambaran manis je. Ada 3 benda perlu tahu:
1. Caruman lebih tinggi berbanding orang muda, sebab risiko kesihatan lebih tinggi.
2. Kalau ada darah tinggi atau kencing manis, permohonan masih boleh diterima, cuma mungkin ada caruman tambahan atau exclusion untuk penyakit tu.
3. Yang paling penting, isytihar sejarah kesihatan dengan jujur. Kalau tak, masa nak claim nanti boleh jadi masalah.

Kalau makcik pakcik rasa caruman berat, ada cara lain: anak-anak boleh kongsi bayar sama-sama. Sedikit seorang, tapi mak ayah dilindungi.

Nak tahu apa pilihan yang sesuai dengan umur dan bajet? Komen "NAK TAHU" atau minta anak-anak WhatsApp saya. Saya Hana, agent AIA, saya terangkan dengan bahasa mudah, takde paksaan.
TXT,
        'angle_cold' => <<<'TXT'
Audiens 50+ yang tak kenal you. Bahasa sopan dan mudah, tiada jargon. Jangan janji "pasti lulus". Kejujuran pasal caruman lebih tinggi ialah yang buat mereka percaya.

Mesej: "Makcik pakcik, ramai yang ingat dah 50 lebih tak boleh ambil takaful lagi. Sebenarnya untuk sesetengah pelan, boleh mohon sehingga umur 70. Komen NAK TAHU, saya terangkan dengan bahasa mudah 🙂"
TXT,
        'angle_warm' => <<<'TXT'
Anak yang komen atau tag adik-beradik. Angle hibah untuk ibu bapa: adik-beradik kongsi bayar caruman, ibu jadi penama. Untuk anak, ni bakti, bukan beban.

Mesej: "Hi [Nama] 🙂 Ramai adik-beradik buat cara ni: semua kongsi bayar caruman setiap bulan, dan mak jadi penama. Kalau apa-apa jadi pada ayah, mak ada duit sendiri tanpa perlu bergantung pada sumbangan anak setiap bulan. Nak I kira kalau kongsi 3 atau 4 orang?"
TXT,
        'angle_hot' => <<<'TXT'
Dah minta harga. Tanya umur sebenar dan sejarah kesihatan (darah tinggi, kencing manis) dengan sopan, terangkan kemungkinan caruman tambahan atau exclusion sebelum bagi angka, supaya tak kecewa nanti.

Mesej: "Untuk saya kira dengan tepat, boleh bagitau umur dan kalau ada darah tinggi atau kencing manis? Bukan untuk tolak ya, cuma ada kemungkinan caruman tambahan atau exclusion, dan saya nak terangkan dulu sebelum bagi angka 🙂"
TXT,
        'key_facts' => <<<'TXT'
• Untuk sesetengah pelan, permohonan diterima sehingga umur 70 tahun. [/go/umur-50]
• Caruman umur 50 lebih tinggi berbanding umur 30 sebab risiko kesihatan lebih tinggi. [/go/umur-50]
• Penyakit sedia ada (darah tinggi, kencing manis): mungkin diterima dengan caj tambahan, exclusion, atau ditolak. Bergantung pada keadaan. [/go/umur-50]
• Pilihan deductible (bayar RM300 atau RM500 pertama) boleh rendahkan caruman. [/go/umur-50]
• Hibah untuk ibu bapa: adik-beradik kongsi bayar, ibu sebagai penama. [/go/hibah-ibu]
• Hibah kecil (RM50k hingga RM100k) cukup sebagai permulaan. [/go/umur-50]
Link: drtakaful.com/go/umur-50, drtakaful.com/go/hibah-ibu
TXT,
    ],

    // ── 12 ──────────────────────────────────────────────────────────────
    12 => [
        'match_title'  => 'Hospital Bill Reality Check',
        'title'        => 'Hospital Bill Reality Check: Teka Berapa?',
        'description'  => 'Bila guna: IG story atau post. Tunjuk bil hospital sebenar (dapat izin, nama dan IC ditutup) atau angka dari drtakaful.com, buat audiens teka dulu, kemudian reveal. Biar nombor yang buat kerja, bukan ayat takut.',
        'category'     => 'content',
        'channel'      => 'instagram',
        'audience'     => 'strangers',
        'product_line' => 'medical',
        'angle_cold' => <<<'TXT'
Story untuk followers yang tak pernah fikir pasal medical card. Poll dulu, reveal kemudian. Jangan tunjuk bil tanpa izin, tutup nama, IC dan nama hospital.

Mesej: "Teka: satu kali masuk wad hospital swasta sebab appendix, berapa bil dia? 🤔 A) RM2k B) RM5k C) RM10k ke atas"
TXT,
        'angle_warm' => <<<'TXT'
Yang undi poll atau reply story. DM dengan jawapan, kemudian satu soalan pasal cover sedia ada. Kebanyakan yang teka rendah ialah yang belum pernah masuk wad swasta.

Mesej: "Hi! Terima kasih undi 🙂 Jawapan dia: boleh cecah RM10k hingga RM15k. Ramai teka rendah. You sekarang ada cover company atau medical card sendiri?"
TXT,
        'angle_hot' => <<<'TXT'
Dia tanya macam mana nak elak bayar sendiri. Hantar ke /go/mc-quote untuk semak caruman 10 saat, atau terus bagi harga contoh umur dia.

Mesej: "Medical card yang I cadangkan had tahunan RM1.5 juta++, takde had seumur hidup. Boleh semak anggaran caruman ikut umur dalam 10 saat sini: drtakaful.com/go/mc-quote, atau bagitau umur, I kira terus 🙂"
TXT,
        'key_facts' => <<<'TXT'
• Satu kali masuk wad di hospital swasta (contoh appendix) boleh cecah RM10k hingga RM15k. [/go/mc-quote]
• Deposit hospital swasta boleh cecah RM3,500 untuk pendaftaran. [/go/anak]
• Pembedahan pintasan jantung (bypass) di hospital swasta: RM60,000 hingga RM80,000. [/go/umur-50]
• Medical card: had tahunan RM1.5 juta++, tiada had seumur hidup, wad dan ICU, pembedahan dan rawatan kanser. [/go/mc-quote]
• Bil sebenar: guna hanya dengan izin, tutup nama, IC, nombor akaun. Jangan kata "klien saya" kalau ia dari berita.
Link: drtakaful.com/go/mc-quote, drtakaful.com/go/kes-sebenar
TXT,
        'steps' => [
            ['title' => 'Story 1: Poll teka bil', 'timing_note' => 'Malam, 8 hingga 10pm',
             'script' => 'Teka: satu kali masuk wad hospital swasta sebab appendix, berapa bil dia? 🤔
Poll: A) RM2k  B) RM5k  C) RM10k ke atas',
             'branch_yes' => 'Ramai undi: tunggu 3 hingga 4 jam, ke Step 2.',
             'branch_no' => 'Sikit undi: tetap teruskan Step 2, kemudian cuba waktu lain minggu depan.'],
            ['title' => 'Story 2: Reveal + satu ayat', 'timing_note' => '3 hingga 4 jam selepas Story 1',
             'script' => 'Jawapan: boleh cecah RM10k hingga RM15k. Itu satu admission je.

[Gambar bil sebenar, nama, IC dan hospital ditutup, atau grafik angka sahaja]

Medical card bukan untuk demam klinik. Ia untuk hari macam ni, supaya duit simpanan tak habis dulu.',
             'branch_yes' => 'Ada yang reply: ke Step 3 dalam DM.',
             'branch_no' => 'Takde reply: ke Step 4 esoknya.'],
            ['title' => 'DM yang undi', 'timing_note' => 'Hari yang sama',
             'script' => 'Hi! Terima kasih undi 🙂 Ramai teka rendah. You sekarang ada cover company atau medical card sendiri?',
             'branch_yes' => 'Ada company: guna strategi "Company Dah Cover, Cukup Ke?". Takde langsung: ke Step 5.',
             'branch_no' => 'Takde balasan: cukup sekali. Jangan follow up DM dari poll.'],
            ['title' => 'Story 3: Salah faham', 'timing_note' => 'Esok',
             'script' => '"Buang duit je kalau tak claim." 🤔
Sebenarnya, tahun tak claim, kredit RM1,000 hingga RM2,500 masuk Health Wallet untuk kos kesihatan anda. Caruman bukan hangus, ia harga untuk tak guna duit simpanan bila hari tu tiba.
Link: drtakaful.com/go/mc-quote',
             'branch_yes' => 'Ada yang tanya: ke Step 5.',
             'branch_no' => 'Simpan ketiga-tiga story dalam Highlight "Medical Card".'],
            ['title' => 'Hantar ke semakan caruman', 'timing_note' => 'Bila ada yang minat',
             'script' => 'Boleh semak anggaran caruman ikut umur dalam 10 saat sini: drtakaful.com/go/mc-quote. Atau bagitau umur, I kira terus 🙂 Saya Hana, agent AIA.',
             'branch_yes' => 'Dia WhatsApp: rekod sebagai lead Hot.',
             'branch_no' => 'Rekod sebagai lead Warm, next contact 2 minggu.'],
        ],
    ],

];
