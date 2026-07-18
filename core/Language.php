<?php
declare(strict_types=1);

session_start();

class Language
{
    private static array $dictionary = [];
    public static string $current = 'tr';
    public static array $supported = ['tr', 'en', 'de', 'fr', 'it', 'nl', 'sv'];

    public static function init(): void
    {
        if (isset($_GET['lang']) && in_array($_GET['lang'], self::$supported)) {
            $_SESSION['lang'] = $_GET['lang'];
        }

        if (isset($_SESSION['lang']) && in_array($_SESSION['lang'], self::$supported)) {
            self::$current = $_SESSION['lang'];
        } 
        elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
            self::$current = in_array($browserLang, self::$supported) ? $browserLang : 'tr';
            $_SESSION['lang'] = self::$current;
        }

        $filePath = __DIR__ . '/../includes/locales/' . self::$current . '.php';
        if (file_exists($filePath)) {
            self::$dictionary = require $filePath;
        }
    }

    public static function get(string $key, array $params = []): string
    {
        $text = self::$dictionary[$key] ?? $key;
        if (empty($params)) return $text;
        
        $replaceKeys = array_map(fn($k) => '{' . $k . '}', array_keys($params));
        return str_replace($replaceKeys, array_values($params), $text);
    }
}

const SUPPORTED_LANGS = ['tr', 'en', 'de', 'fr', 'it', 'nl', 'sv'];

function t(string $key, array $params = []): string {
    return Language::get($key, $params);
}

function current_lang(): string {
    return Language::$current;
}

// Menüde görünecek bayraklar ve isimler
// Emoji flag yerine flag-icons CSS sınıfları kullanılır — tüm tarayıcılarda çalışır (Windows dahil)
function lang_label(string $code): string {
    // ISO 3166-1 alpha-2 ülke kodu (flag-icons sınıfı için)
    $flagCodes = [
        'tr' => 'tr',
        'en' => 'gb',
        'de' => 'de',
        'fr' => 'fr',
        'it' => 'it',
        'nl' => 'nl',
        'sv' => 'se',
    ];
    $names = [
        'tr' => 'Türkçe',
        'en' => 'English',
        'de' => 'Deutsch',
        'fr' => 'Français',
        'it' => 'Italiano',
        'nl' => 'Nederlands',
        'sv' => 'Svenska',
    ];
    $cc   = htmlspecialchars($flagCodes[$code] ?? $code, ENT_QUOTES, 'UTF-8');
    $name = htmlspecialchars($names[$code] ?? strtoupper($code), ENT_QUOTES, 'UTF-8');
    // Güvenli HTML döner — çağıran yerde htmlspecialchars() sarmalamayın
    return '<span class="fi fi-' . $cc . '" aria-hidden="true" style="border-radius:2px;"></span> ' . $name;
}

function lang_url(string $code): string {
    return "?lang=" . $code;
}