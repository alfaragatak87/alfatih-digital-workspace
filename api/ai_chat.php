<?php
session_start();
header('Content-Type: application/json');

// Pastikan pengguna sudah login
if (empty($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.']);
    exit;
}

$user_name = $_SESSION['nama'] ?? 'Pengguna';

// Ambil konfigurasi (koneksi.php mendefinisikan konstanta, termasuk API Key)
require_once '../00_sistem_inti/1_koneksi_database.php';

// Cek ketersediaan API Key
$API_KEY = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';

if (empty($API_KEY) || $API_KEY === 'MASUKKAN_API_KEY_ANDA_DISINI') {
    echo json_encode(['status' => 'error', 'message' => 'Sistem AI belum dikonfigurasi. Silakan hubungi admin untuk menyetel GEMINI_API_KEY.']);
    exit;
}

$message = $_POST['message'] ?? '';
$file = $_FILES['file'] ?? null;

// Fallback untuk backward compatibility jika dikirim via JSON
if (empty($_POST) && empty($_FILES)) {
    $input = json_decode(file_get_contents('php://input'), true);
    $message = $input['message'] ?? '';
}

$imageData = null;
$mimeType = null;
$tempFilePath = null;
$originalFileName = null;

if ($file && $file['error'] === UPLOAD_ERR_OK) {
    $mimeType = $file['type'];
    $originalFileName = $file['name'];
    
    if (strpos($mimeType, 'image/') === 0) {
        $imageData = base64_encode(file_get_contents($file['tmp_name']));
    }
    
    $ext = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $uniqueName = uniqid('ai_temp_') . '.' . $ext;
    $targetDir = '../08_unggahan/files/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    
    $targetPath = $targetDir . $uniqueName;
    move_uploaded_file($file['tmp_name'], $targetPath);
    $tempFilePath = $uniqueName;
}

if (empty(trim($message)) && !$tempFilePath) {
    echo json_encode(['status' => 'error', 'message' => 'Pesan atau lampiran tidak boleh kosong.']);
    exit;
}

date_default_timezone_set('Asia/Jakarta');
$jam = date('H:i');

if ($message === '[SYSTEM_MIDNIGHT_GREETING]') {
    $message = "Ini adalah pesan otomatis sistem: Aku baru saja login ke sistem pada jam $jam pagi (tengah malam). Tolong berikan sapaan pertama berupa candaan ringan atau pertanyaan iseng kenapa aku belum tidur jam segini. Jangan kaku, sapa dengan ramah dan singkat saja 1-2 kalimat.";
}

$url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $API_KEY;

// Pesan instruksi sistem untuk membentuk persona AI
$systemPrompt = "Kamu adalah Alfatih AI, asisten virtual cerdas yang terintegrasi di dalam Alfatih Workspace. 
Alfatih Workspace adalah platform manajemen file dan portofolio.
Tugasmu:
1. Membantu pengguna (user) menjawab pertanyaan seputar pekerjaan, manajemen data, coding, analisis gambar, atau pertanyaan umum.
2. Jawab dengan singkat, padat, dan jelas (maksimal 2-3 paragraf).
3. Gunakan bahasa Indonesia yang santai, modern, namun tetap profesional.
4. Jangan gunakan format yang terlalu rumit (markdown table dll), cukup gunakan *bold* atau list bullet biasa.
5. Bersikaplah ramah dan sedikit humoris jika situasinya pas.
6. Jika ada file yang dilampirkan, kamu bisa melihat info nama file dan ID sementaranya. Jika user menyuruhmu 'menyimpan file', maka panggil fungsi simpan_ke_file_manager.
PENTING: Saat ini kamu sedang berbicara dengan pengguna bernama {$user_name}. Sapalah namanya sesekali jika relevan.";

if (!isset($_SESSION['ai_history'])) {
    $_SESSION['ai_history'] = [];
}

// Tambahkan teks user ke histori, jika ada gambar/file, kasih tau AI
$historyMsg = $message;
if ($tempFilePath) {
    if ($imageData) {
        $historyMsg = $message ? $message . "\n[SISTEM: User melampirkan gambar dengan nama: $originalFileName (ID sementara: $tempFilePath)]" : "[SISTEM: User melampirkan gambar dengan nama: $originalFileName (ID sementara: $tempFilePath)]";
    } else {
        $historyMsg = $message ? $message . "\n[SISTEM: User melampirkan dokumen bernama: $originalFileName (ID sementara: $tempFilePath)]" : "[SISTEM: User melampirkan dokumen bernama: $originalFileName (ID sementara: $tempFilePath)]";
    }
}

$_SESSION['ai_history'][] = [
    "role" => "user",
    "parts" => [["text" => $historyMsg]]
];

// Batasi histori maksimal 10 pesan terakhir (5 putaran interaksi)
if (count($_SESSION['ai_history']) > 10) {
    $_SESSION['ai_history'] = array_slice($_SESSION['ai_history'], -10);
}

// Persiapkan contents untuk dikirim ke Gemini
$apiContents = $_SESSION['ai_history'];

// Jika ada image data, timpa part terakhir (pesan user saat ini) dengan format multimodal
if ($imageData) {
    $lastIndex = count($apiContents) - 1;
    $apiContents[$lastIndex]['parts'] = [];
    if (!empty(trim($message))) {
        $apiContents[$lastIndex]['parts'][] = ["text" => $message];
    }
    $apiContents[$lastIndex]['parts'][] = [
        "inlineData" => [
            "mimeType" => $mimeType,
            "data" => $imageData
        ]
    ];
}

$data = [
    "contents" => $apiContents,
    "systemInstruction" => [
        "parts" => [
            ["text" => $systemPrompt]
        ]
    ],
    "tools" => [
        [
            "functionDeclarations" => [
                [
                    "name" => "buat_folder",
                    "description" => "Membuat folder baru di dalam File Manager / Workspace pengguna.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "nama_folder" => [
                                "type" => "STRING",
                                "description" => "Nama folder yang ingin dibuat."
                            ],
                            "deskripsi" => [
                                "type" => "STRING",
                                "description" => "Deskripsi opsional untuk folder ini."
                            ]
                        ],
                        "required" => ["nama_folder"]
                    ]
                ],
                [
                    "name" => "isi_data_cv",
                    "description" => "Mengisi atau memperbarui profil data diri CV pengguna di CV Builder.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "nama_lengkap" => ["type" => "STRING"],
                            "title" => ["type" => "STRING", "description" => "Judul profesi / pekerjaan (misal: Software Engineer)"],
                            "description" => ["type" => "STRING", "description" => "Ringkasan profesional tentang pengguna"]
                        ],
                        "required" => ["nama_lengkap", "title"]
                    ]
                ],
                [
                    "name" => "simpan_ke_file_manager",
                    "description" => "Menyimpan lampiran file ke dalam File Manager secara permanen jika pengguna memintanya. Harus dipanggil dengan parameter file_id_sementara dan nama_file_asli.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "file_id_sementara" => [
                                "type" => "STRING",
                                "description" => "ID atau nama file sementara (contoh: ai_temp_123.pdf)"
                            ],
                            "nama_file_asli" => [
                                "type" => "STRING",
                                "description" => "Nama file asli yang akan disimpan"
                            ]
                        ],
                        "required" => ["file_id_sementara", "nama_file_asli"]
                    ]
                ],
                [
                    "name" => "cari_file_di_drive",
                    "description" => "Mencari file di dalam File Manager / Drive berdasarkan kata kunci. Akan mengembalikan daftar file beserta link download-nya.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "kata_kunci" => [
                                "type" => "STRING",
                                "description" => "Kata kunci atau nama file yang dicari"
                            ]
                        ],
                        "required" => ["kata_kunci"]
                    ]
                ],
                [
                    "name" => "baca_struktur_drive",
                    "description" => "Membaca dan menampilkan daftar semua file dan folder yang ada di direktori root File Manager.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "dummy" => ["type" => "STRING", "description" => "biarkan kosong"]
                        ]
                    ]
                ],
                [
                    "name" => "rapikan_file_otomatis",
                    "description" => "Otomatis merapikan seluruh file yang berserakan di direktori luar (root). File akan dikelompokkan dan dipindah ke dalam folder Dokumen, Gambar, atau Arsip berdasarkan ekstensinya.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "dummy" => ["type" => "STRING", "description" => "biarkan kosong"]
                        ]
                    ]
                ],
                [
                    "name" => "entri_data_ktp",
                    "description" => "Digunakan untuk menyimpan data KTP hasil OCR/Vision ke database Penduduk.",
                    "parameters" => [
                        "type" => "OBJECT",
                        "properties" => [
                            "nik" => ["type" => "STRING"],
                            "nama" => ["type" => "STRING"],
                            "tempat_lahir" => ["type" => "STRING"],
                            "tanggal_lahir" => ["type" => "STRING", "description" => "Format YYYY-MM-DD"],
                            "jenis_kelamin" => ["type" => "STRING", "description" => "Laki-Laki / Perempuan"],
                            "alamat" => ["type" => "STRING"],
                            "agama" => ["type" => "STRING"],
                            "pekerjaan" => ["type" => "STRING"]
                        ],
                        "required" => ["nik", "nama"]
                    ]
                ]
            ]
        ]
    ],
    "generationConfig" => [
        "temperature" => 0.7,
        "maxOutputTokens" => 500
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

// Bypass SSL verification if needed (disarankan untuk hosting dengan masalah sertifikat cURL lokal)
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// Paksa penggunaan IPv4 untuk menghindari masalah timeout di beberapa shared hosting
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
// Set timeout maksimal 60 detik agar tidak menggantung tanpa batas waktu, namun cukup untuk balasan panjang
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode === 200) {
    $result = json_decode($response, true);
    
    // Cek apakah ada Function Call
    if (isset($result['candidates'][0]['content']['parts'][0]['functionCall'])) {
        $func = $result['candidates'][0]['content']['parts'][0]['functionCall'];
        $fName = $func['name'];
        $fArgs = $func['args'];
        
        $replyText = "";
        
        if ($fName === 'buat_folder') {
            $nama_folder = $fArgs['nama_folder'] ?? 'Folder Baru';
            $deskripsi = $fArgs['deskripsi'] ?? 'Dibuat otomatis oleh Alfatih AI';
            $parent_id = 0;
            $owner = $_SESSION['username'];
            $icon = 'fa-folder';
            $warna = '#3b82f6';
            
            $stmt = $mysqli->prepare("INSERT INTO folders (parent_id, owner_username, nama_folder, icon, warna, deskripsi) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("isssss", $parent_id, $owner, $nama_folder, $icon, $warna, $deskripsi);
            if ($stmt->execute()) {
                $replyText = "✅ Siap! Folder **$nama_folder** telah berhasil aku buat di File Manager kamu.";
            } else {
                $replyText = "❌ Maaf, aku gagal membuat folder: " . $mysqli->error;
            }
        } 
        else if ($fName === 'isi_data_cv') {
            $nama_lengkap = $fArgs['nama_lengkap'] ?? '';
            $title = $fArgs['title'] ?? '';
            $desc = $fArgs['description'] ?? '';
            $uid = $_SESSION['uid'];
            
            $stmt = $mysqli->prepare("SELECT profile_data FROM users WHERE id=?");
            $stmt->bind_param("i", $uid);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            $cvData = $row['profile_data'] ? json_decode($row['profile_data'], true) : [];
            
            if (!$cvData) $cvData = [];
            if (!isset($cvData['identity'])) $cvData['identity'] = [];
            
            if ($nama_lengkap) $cvData['identity']['name'] = $nama_lengkap;
            if ($title) $cvData['identity']['title'] = $title;
            if ($desc) $cvData['identity']['desc'] = $desc;
            
            $jsonStr = json_encode($cvData);
            
            $stmt2 = $mysqli->prepare("UPDATE users SET profile_data=? WHERE id=?");
            $stmt2->bind_param("si", $jsonStr, $uid);
            if ($stmt2->execute()) {
                $replyText = "✅ Data profil CV-mu sudah berhasil ku-update!\n\nNama: **$nama_lengkap**\nProfesi: **$title**\n\nSilakan cek langsung di menu CV Builder.";
            } else {
                $replyText = "❌ Maaf, aku gagal mengupdate CV: " . $mysqli->error;
            }
        } else if ($fName === 'simpan_ke_file_manager') {
            $file_id = $fArgs['file_id_sementara'] ?? '';
            $nama_file_asli = $fArgs['nama_file_asli'] ?? '';
            $owner = $_SESSION['username'];
            $parent_id = 0; // Root folder
            
            $targetDir = '../08_unggahan/files/';
            $tempPath = $targetDir . $file_id;
            
            if (!empty($file_id) && file_exists($tempPath)) {
                $newFileName = str_replace('ai_temp_', 'ai_file_', $file_id);
                $newPath = $targetDir . $newFileName;
                rename($tempPath, $newPath);
                
                $stmt = $mysqli->prepare("INSERT INTO files (folder_id, owner_username, jenis, nama_file, file_path, tags) VALUES (?,?,'file',?,?,?)");
                $tags = 'ai-upload';
                $stmt->bind_param("issss", $parent_id, $owner, $nama_file_asli, $newFileName, $tags);
                
                if ($stmt->execute()) {
                    $replyText = "✅ Siap! File **$nama_file_asli** telah berhasil disimpan ke dalam File Manager.";
                } else {
                    $replyText = "❌ Maaf, aku gagal menyimpan data file ke database: " . $mysqli->error;
                }
            } else {
                $replyText = "❌ Maaf, file sementara ($file_id) tidak ditemukan atau mungkin belum terunggah dengan sempurna.";
            }
        } else if ($fName === 'cari_file_di_drive') {
            $keyword = $fArgs['kata_kunci'] ?? '';
            $owner = $_SESSION['username'];
            $keywordLike = "%$keyword%";
            
            $stmt = $mysqli->prepare("SELECT id, nama_file FROM files WHERE owner_username=? AND nama_file LIKE ? LIMIT 10");
            $stmt->bind_param("ss", $owner, $keywordLike);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($res->num_rows > 0) {
                $replyText = "🔍 Aku menemukan beberapa file yang cocok dengan kata kunci **$keyword**:\n\n";
                while($row = $res->fetch_assoc()) {
                    $dlLink = "index.php?page=workspace&action=download_file&file_id=" . $row['id'];
                    $replyText .= "- [{$row['nama_file']}]($dlLink)\n";
                }
                $replyText .= "\nSilakan klik tautan di atas untuk mendownloadnya langsung.";
            } else {
                $replyText = "❌ Maaf, aku tidak menemukan file apapun yang mengandung kata kunci **$keyword** di Drive-mu.";
            }
        } else if ($fName === 'baca_struktur_drive') {
            $owner = $_SESSION['username'];
            
            $stmt1 = $mysqli->prepare("SELECT nama_folder FROM folders WHERE owner_username=? AND parent_id=0");
            $stmt1->bind_param("s", $owner);
            $stmt1->execute();
            $folders = $stmt1->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $stmt2 = $mysqli->prepare("SELECT nama_file FROM files WHERE owner_username=? AND folder_id=0");
            $stmt2->bind_param("s", $owner);
            $stmt2->execute();
            $files = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
            
            $replyText = "📂 **Katalog Direktori Utama (Root)**\n\n";
            if (count($folders) > 0) {
                $replyText .= "**Folder:**\n";
                foreach($folders as $f) $replyText .= "- 📁 " . $f['nama_folder'] . "\n";
            }
            if (count($files) > 0) {
                $replyText .= "\n**File:**\n";
                foreach($files as $f) $replyText .= "- 📄 " . $f['nama_file'] . "\n";
            }
            
            if (count($folders) == 0 && count($files) == 0) {
                $replyText = "📂 Direktori utama Drive-mu saat ini kosong.";
            }
        } else if ($fName === 'rapikan_file_otomatis') {
            $owner = $_SESSION['username'];
            
            $stmt = $mysqli->prepare("SELECT id, nama_file FROM files WHERE owner_username=? AND folder_id=0");
            $stmt->bind_param("s", $owner);
            $stmt->execute();
            $res = $stmt->get_result();
            
            if ($res->num_rows == 0) {
                $replyText = "👍 Wah, file di luar (root) Drive-mu sudah kosong/rapi. Tidak ada yang perlu dirapikan lagi!";
            } else {
                $kategori = [
                    'Gambar' => ['jpg','jpeg','png','gif','svg','webp'],
                    'Dokumen' => ['pdf','doc','docx','xls','xlsx','ppt','pptx','txt','csv'],
                    'Arsip' => ['zip','rar','tar','gz','7z'],
                    'Video' => ['mp4','avi','mkv','mov']
                ];
                
                $getFolderId = function($nama_folder) use ($mysqli, $owner) {
                    $s = $mysqli->prepare("SELECT id FROM folders WHERE owner_username=? AND parent_id=0 AND nama_folder=?");
                    $s->bind_param("ss", $owner, $nama_folder);
                    $s->execute();
                    $r = $s->get_result();
                    if ($r->num_rows > 0) {
                        return $r->fetch_assoc()['id'];
                    } else {
                        $icon = 'fa-folder';
                        if ($nama_folder == 'Gambar') $icon = 'fa-image';
                        if ($nama_folder == 'Dokumen') $icon = 'fa-file-lines';
                        if ($nama_folder == 'Arsip') $icon = 'fa-file-zipper';
                        if ($nama_folder == 'Video') $icon = 'fa-film';
                        
                        $ins = $mysqli->prepare("INSERT INTO folders (parent_id, owner_username, nama_folder, icon, warna, deskripsi) VALUES (0, ?, ?, ?, '#3b82f6', 'Dibuat otomatis oleh AI')");
                        $ins->bind_param("sss", $owner, $nama_folder, $icon);
                        $ins->execute();
                        return $ins->insert_id;
                    }
                };
                
                $movedCount = 0;
                while($row = $res->fetch_assoc()) {
                    $ext = strtolower(pathinfo($row['nama_file'], PATHINFO_EXTENSION));
                    $targetFolder = 'Lainnya';
                    
                    foreach($kategori as $namaKat => $exts) {
                        if (in_array($ext, $exts)) {
                            $targetFolder = $namaKat;
                            break;
                        }
                    }
                    
                    $fid = $getFolderId($targetFolder);
                    
                    $upd = $mysqli->prepare("UPDATE files SET folder_id=? WHERE id=?");
                    $upd->bind_param("ii", $fid, $row['id']);
                    $upd->execute();
                    $movedCount++;
                }
                
                $replyText = "✨ **Simsalabim!** Aku telah menyapu bersih dan merapikan **$movedCount file** ke dalam folder kategorinya masing-masing (Gambar, Dokumen, Arsip, dll). Silakan cek File Manager kamu!";
            }
            }
        } else if ($fName === 'entri_data_ktp') {
            if (($_SESSION['profesi'] ?? '') === 'Pegawai Balai Desa' || ($_SESSION['role'] ?? '') === 'superadmin') {
                $nik = $fArgs['nik'] ?? '';
                $nama = $fArgs['nama'] ?? '';
                $tempat_lahir = $fArgs['tempat_lahir'] ?? '';
                $tanggal_lahir = $fArgs['tanggal_lahir'] ?? null;
                $jenis_kelamin = $fArgs['jenis_kelamin'] ?? 'Laki-Laki';
                $alamat = $fArgs['alamat'] ?? '';
                $agama = $fArgs['agama'] ?? '';
                $pekerjaan = $fArgs['pekerjaan'] ?? '';
                $owner = $_SESSION['username'];
                
                if (empty($tanggal_lahir)) $tanggal_lahir = null; // Prevent invalid date format
                
                $stmt = $mysqli->prepare("INSERT INTO penduduk (owner_username, nik, nama, tempat_lahir, tanggal_lahir, jenis_kelamin, alamat, agama, pekerjaan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $stmt->bind_param("sssssssss", $owner, $nik, $nama, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $alamat, $agama, $pekerjaan);
                    if ($stmt->execute()) {
                        $replyText = "✅ **Berhasil!** Data KTP atas nama **$nama** ($nik) telah berhasil saya simpan ke dalam database Data Warga.";
                    } else {
                        $replyText = "❌ Gagal menyimpan data KTP: " . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $replyText = "❌ Terjadi kesalahan query: " . $mysqli->error;
                }
            } else {
                $replyText = "⚠️ Maaf, Anda tidak memiliki akses untuk mengentri data kependudukan. Fitur ini khusus untuk **Pegawai Balai Desa**.";
            }
        }
        $_SESSION['ai_history'][] = [
            "role" => "model",
            "parts" => [["text" => $replyText]]
        ];

        echo json_encode(['status' => 'success', 'reply' => $replyText]);
        exit;
    }
    
    if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        $reply = $result['candidates'][0]['content']['parts'][0]['text'];
        
        // Simpan balasan AI ke histori
        $_SESSION['ai_history'][] = [
            "role" => "model",
            "parts" => [["text" => $reply]]
        ];

        echo json_encode(['status' => 'success', 'reply' => trim($reply)]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Format balasan dari AI tidak dikenali.']);
    }
} else {
    // Menangkap error dari Gemini
    $err = json_decode($response, true);
    $errMsg = $err['error']['message'] ?? "Kode Error: " . $httpCode;
    echo json_encode(['status' => 'error', 'message' => 'Error AI: ' . $errMsg]);
}
