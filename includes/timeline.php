<?php
declare(strict_types=1);

/**
 * Kariyer (Deneyim) Zaman Çizelgesi Verileri Akıllı Motoru
 * Ziyaretçinin seçtiği dile göre ilgili veri dosyasını okur.
 */
function timeline_data(string $lang): array {
    
    // Güvenlik Kalkanı: Sadece desteklediğimiz dillerin yüklenmesine izin veriyoruz
    $allowed_langs = ['tr', 'en', 'de', 'fr', 'it', 'nl', 'sv'];
    
    // Eğer desteklenmeyen bir dil istenirse otomatik olarak Türkçe'ye dön
    if (!in_array($lang, $allowed_langs)) {
        $lang = 'tr'; 
    }

    $filePath = __DIR__ . '/timeline_data/' . $lang . '.php';

    // Dosya mevcutsa oku ve siteye gönder
    if (file_exists($filePath)) {
        return require $filePath;
    }

    // Dosya bulunamazsa site çökmesin diye boş veri dön
    return []; 
}