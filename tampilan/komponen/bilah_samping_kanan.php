<?php
// +------------------------------------------------------------------------------+
// ¦  FILE: tampilan/komponen/bilah_samping_kanan.php                             ¦
// ¦                                                                              ¦
// ¦  DESKRIPSI:                                                                  ¦
// ¦  Panel informasi samping kanan yang menampilkan limit kuota penyimpanan      ¦
// ¦  (Storage Bar) beserta visualisasi ringkas profil login saat ini.            ¦
// ¦                                                                              ¦
// ¦  KONEKSI & RELASI:                                                           ¦
// ¦  - Bergantung pada kalkulasi ukuran byte ($size_used, $storage_pct)      ¦
// ¦    yang diproses di awal file index.php.                                   ¦
// ¦                                                                              ¦
// ¦  BARIS KODE PENTING:                                                         ¦
// ¦  - <div class="storage-fill" style="width:<?=  ?>%;"> : Bar    ¦
// ¦    persentase penyimpanan yang menyesuaikan ruang fisik file aktual.         ¦
// +------------------------------------------------------------------------------+
?>
<div class="right-sidebar" id="rightSidebar">
    <div class="rs-header">
        <h3 id="rs_title"><i class="fa-solid fa-circle-info"></i> Detail Item</h3>
        <button class="btn-icon" onclick="toggleRightSidebar()" style="width:30px;height:30px;font-size:.85rem;border:none;"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="rs-content">
        <div id="rs_icon" class="rs-preview"><i class="fa-solid fa-folder" style="font-size:3rem;"></i></div>
        <div id="rs_actions" class="rs-action-buttons"></div>
        <div class="rs-qr-box" id="rs_qr_container"><img id="rs_qr_img" src="" alt="QR Code"><p>Scan QR untuk berbagi</p></div>
        <div class="rs-group"><label>Nama</label><div class="rs-val" id="rs_name">&mdash;</div></div>
        <div class="rs-group"><label>Jenis</label><div class="rs-val" id="rs_type">&mdash;</div></div>
        <div class="rs-group"><label>Pemilik</label><div class="rs-val" id="rs_owner">&mdash;</div></div>
        <div class="rs-group"><label>Tanggal</label><div class="rs-val" id="rs_date">&mdash;</div></div>
        <div class="rs-group"><label>Ukuran</label><div class="rs-val" id="rs_size">&mdash;</div></div>
        <div class="rs-group"><label>Catatan</label><div class="rs-val" id="rs_desc">&mdash;</div></div>
        <div class="rs-group"><label>Label</label><div class="rs-val" id="rs_tags">&mdash;</div></div>
    </div>
</div>