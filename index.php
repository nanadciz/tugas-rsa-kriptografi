<!DOCTYPE html>
<html>
<head><title>RSA Web Tool</title></head>
<body>
    <h2>Alat Kriptografi Asimetris (RSA)</h2>

    <form method="POST" action="">
        <label>Pilih Aksi Eksekusi:</label><br>
        <select name="aksi" required>
            <option value="generate">1. Buat Kunci Baru (RSA Key Generation)</option>
            <option value="enkripsi">2. Enkripsi (Butuh Pesan Asli & Public Key)</option>
            <option value="dekripsi">3. Dekripsi (Butuh Ciphertext & Private Key)</option>
        </select><br><br>

        <label>Input Teks / Pesan / Ciphertext:</label><br>
        <textarea name="pesan_input" rows="3" cols="60"></textarea><br><br>

        <label>Input Kunci (.pem string):</label><br>
        <textarea name="kunci_input" rows="5" cols="60"></textarea><br><br>

        <button type="submit">Proses Sekarang</button>
    </form>
</body>
</html>

<?php
$hasil_teks = ""; // Variabel default kosong

// Fungsi 1: Membangkitkan Pasangan Kunci RSA
function generate_rsa_keys() {
    // Konfigurasi kekuatan algoritma RSA
    $config = [
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ];

    // Mesin PHP mengeksekusi pembuatan kunci
    $res = openssl_pkey_new($config);

    // Ekstrak Private Key (.pem format)
    openssl_pkey_export($res, $private_key);

    // Ekstrak Public Key (.pem format)
    $key_details = openssl_pkey_get_details($res);
    $public_key = $key_details["key"];

    return [
        'public' => $public_key,
        'private' => $private_key
    ];
}
// (Jangan tutup tag php)


// Fungsi 2: Enkripsi dengan Public Key
function rsa_encrypt($pesan, $public_key) {
    // Proses enkripsi asimetris
    openssl_public_encrypt($pesan, $cipher_biner, $public_key);
    
    // Teks di-encode menjadi string Base64 (A-Z, a-z, 0-9)
    return base64_encode($cipher_biner);
}

// Fungsi 3: Dekripsi dengan Private Key
function rsa_decrypt($cipher_base64, $private_key) {
    // Kembalikan Base64 menjadi biner acak aslinya
    $cipher_biner = base64_decode($cipher_base64);
    
    // Buka paksa kunci matematis dengan private key
    openssl_private_decrypt($cipher_biner, $pesan_asli, $private_key);
    
    return $pesan_asli;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Menangkap data dari formulir HTML
    $aksi  = $_POST['aksi'];
    $pesan = $_POST['pesan_input'];
    $kunci = $_POST['kunci_input'];

    // Penyeleksian Aksi
    if ($aksi == 'generate') {
        $keys = generate_rsa_keys();
        $hasil_teks = "=== PUBLIC KEY ===\n" . $keys['public'] . 
                      "\n=== PRIVATE KEY ===\n" . $keys['private'];
    } 
    else if ($aksi == 'enkripsi') {
        $hasil_teks = "Hasil Enkripsi (Ciphertext):\n" . 
                      rsa_encrypt($pesan, $kunci);
    } 
    else if ($aksi == 'dekripsi') {
        $hasil_teks = "Hasil Dekripsi (Pesan Asli):\n" . 
                      rsa_decrypt($pesan, $kunci);
    }
}
?> 
<!-- Tutup tag PHP. Di bawah ini letakkan kode HTML Langkah 1 -->
 
    <!-- Potongan ini diletakkan di bawah tag </form> di HTML Langkah 1 -->
    
    <hr>
    <h3>Layar Output Server:</h3>
    
    <!-- Textarea akan terisi otomatis dari respon PHP di atas -->
    <textarea rows="15" cols="60" readonly style="background:#f1f5f9;"><?= htmlspecialchars($hasil_teks); ?></textarea>
    
</body>
</html>