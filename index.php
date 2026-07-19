<?php
/**
 * Özgür Önder - Kişisel Web Sitesi
 */

require_once __DIR__ . '/core/Language.php';
Language::init();

require_once __DIR__ . '/includes/timeline.php';
$currentLang = current_lang();
$timelineData = timeline_data($currentLang);
$timelineYears = array_map('intval', array_keys($timelineData));
rsort($timelineYears);
$defaultYear = $timelineYears[0] ?? date('Y');

$currentUrl = lang_url($currentLang);
$defaultLang = SUPPORTED_LANGS[0] ?? $currentLang;
$defaultUrl = lang_url($defaultLang);
$siteName = t('site.name');
$metaDescription = t('meta.description');
$siteBase = 'https://ozguronder.com.tr/';
$socialImage = $siteBase . 'Logo.png';

// ── Power BI Demo Kimlik Bilgileri ────────────────────────────────────────
// Gerçek demo hesabınızın bilgilerini buraya girin:
$pbiEmail    = 'demo@ozguronder.com.tr';   // ← gerçek demo e-postanızı yazın
$pbiPassword = 'Demo2024!';                 // ← gerçek demo şifrenizi yazın
// Raporun doğrudan paylaşım URL'si (embed değil, Power BI'dan "Paylaş" linki):
$pbiHrUrl    = 'https://app.powerbi.com/reportEmbed?reportId=9aabcef7-7fcd-4b10-8791-244333accd71&autoAuth=true&ctid=08005930-ab3e-4ee4-8f83-476daad83a73';
// ─────────────────────────────────────────────────────────────────────────

// Form işleme
$formSubmitted = false;
$formSuccess = false;
$formMessage = '';

if (isset($_SERVER["REQUEST_METHOD"]) && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_form'])) {
    $formSubmitted = true;
    
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $subject = htmlspecialchars($_POST['subject'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');

    $to = "info@ozguronder.com.tr";
    $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8\r\n";
    $body = "Ad: $name\nE-posta: $email\nKonu: $subject\nMesaj:\n$message";

    if (mail($to, $subject, $body, $headers)) {
        $formSuccess = true;
        $formMessage = t('form.success');
    } else {
        $formSuccess = false;
        $formMessage = t('form.error');
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($currentLang, ENT_QUOTES, 'UTF-8'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <?php foreach (SUPPORTED_LANGS as $lang): ?>
        <link rel="alternate" hreflang="<?php echo htmlspecialchars($lang, ENT_QUOTES, 'UTF-8'); ?>" href="<?php echo htmlspecialchars(lang_url($lang), ENT_QUOTES, 'UTF-8'); ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?php echo htmlspecialchars($defaultUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:url" content="<?php echo htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>">
    <meta property="og:site_name" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($metaDescription, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#0a0e14">
    
    <link rel="shortcut icon" href="Logo.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flag-icons@7.2.3/css/flag-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css?v=11.0">
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": "Özgür Önder",
        "url": "<?php echo htmlspecialchars($siteBase, ENT_QUOTES, 'UTF-8'); ?>",
        "jobTitle": "Senior Team Lead",
        "image": "<?php echo htmlspecialchars($socialImage, ENT_QUOTES, 'UTF-8'); ?>",
        "email": "info@ozguronder.com.tr",
        "sameAs": [
            "https://www.linkedin.com/in/ozgurronderr/",
            "https://github.com/ozgur-onder",
            "https://t.me/ozguronder"
        ]
    }
    </script>
    <script>if('scrollRestoration'in history){history.scrollRestoration='manual';}</script>
</head>
<body data-bs-spy="scroll" data-bs-target="#mainNav" data-bs-offset="77">

    <nav class="navbar navbar-expand-lg fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="Logo.png" alt="Özgür Önder" class="navbar-logo me-2">
                <span class="brand-name">Özgür <span>ÖNDER</span></span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#hero"><?php echo htmlspecialchars(t('nav.home'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#hakkimda"><?php echo htmlspecialchars(t('nav.about'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#yetkinlikler"><?php echo htmlspecialchars(t('nav.skills'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#deneyim"><?php echo htmlspecialchars(t('nav.career'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#raporlar"><?php echo htmlspecialchars(t('nav.reports'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    <li class="nav-item"><a class="nav-link" href="#iletisim"><?php echo htmlspecialchars(t('nav.contact'), ENT_QUOTES, 'UTF-8'); ?></a></li>
                    
                    <li class="nav-item dropdown ms-lg-2">
                        <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo lang_label(current_lang()); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
                            <?php foreach (SUPPORTED_LANGS as $lang): ?>
                                <li>
                                    <a class="dropdown-item" href="<?php echo htmlspecialchars(lang_url($lang), ENT_QUOTES, 'UTF-8'); ?>">
                                        <?php echo lang_label($lang); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero-section" id="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="section-tag"><?php echo htmlspecialchars(t('hero.tag'), ENT_QUOTES, 'UTF-8'); ?></span>
                    <h1 class="hero-title">
                        <?php echo t('hero.title_html'); ?>
                    </h1>
                    <p class="hero-subtitle">
                        <?php echo htmlspecialchars(t('hero.subtitle'), ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                    <p class="hero-quote"><?php echo t('hero.quote_html'); ?></p>
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <a href="#iletisim" class="btn btn-outline-light js-scroll-link">
                            <i class="bi bi-envelope me-2"></i><?php echo htmlspecialchars(t('hero.cta'), ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                        <div class="social-links">
                            <a href="https://www.linkedin.com/in/ozgurronderr/" target="_blank" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
                            <a href="https://github.com/ozgur-onder" target="_blank" title="GitHub"><i class="bi bi-github"></i></a>
                            <a href="https://t.me/ozguronder" target="_blank" title="Telegram"><i class="bi bi-telegram"></i></a>
                            <a href="https://wa.me/905449800988" target="_blank" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>                                    
                        </div>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <div class="hero-image-container">
                        <img src="Özgür ÖNDER.jpeg" alt="Özgür Önder" class="hero-profile-img">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="hakkimda" class="section-dark">
        <div class="container">
            <div class="row g-4 align-items-center">
                <div class="col-lg-5 text-center mb-4 mb-lg-0">
                    <div class="about-image-wrapper mx-auto">
                        <img src="Özgür ÖNDER.jpeg" alt="Özgür Önder" class="about-img">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="about-content">
                        <h2 class="about-title"><?php echo t('about.title_html'); ?></h2>
                        <p class="about-intro"><?php echo t('about.intro_html'); ?></p>
                        <div id="aboutBody" class="about-body-collapsible is-collapsed">
                            <p><?php echo htmlspecialchars(t('about.body'), ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="about-highlights">
                                <div class="highlight-item">
                                    <i class="bi bi-graph-up-arrow"></i>
                                    <span><?php echo htmlspecialchars(t('about.highlight1'), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="highlight-item">
                                    <i class="bi bi-people"></i>
                                    <span><?php echo htmlspecialchars(t('about.highlight2'), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <div class="highlight-item">
                                    <i class="bi bi-gear"></i>
                                    <span><?php echo htmlspecialchars(t('about.highlight3'), ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="about-readmore btn btn-outline-light btn-sm" aria-expanded="false" aria-controls="aboutBody">Devamını Gör</button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="yetkinlikler">
        <div class="container">
            <div class="row g-3 align-items-stretch">
                <div class="col-lg-6 d-flex">
                    <div class="skills-panel w-100">
                        <h3 class="panel-title"><i class="bi bi-star-fill"></i> <?php echo htmlspecialchars(t('skills.core.title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="skills-list">
                            <div class="skill-row">
                                <div class="skill-icon"><i class="bi bi-people-fill"></i></div>
                                <div class="skill-info">
                                    <h5><?php echo htmlspecialchars(t('skills.core.item1.title'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars(t('skills.core.item1.desc'), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="skill-row">
                                <div class="skill-icon"><i class="bi bi-graph-up-arrow"></i></div>
                                <div class="skill-info">
                                    <h5><?php echo htmlspecialchars(t('skills.core.item2.title'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars(t('skills.core.item2.desc'), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="skill-row">
                                <div class="skill-icon"><i class="bi bi-gear-wide-connected"></i></div>
                                <div class="skill-info">
                                    <h5><?php echo htmlspecialchars(t('skills.core.item3.title'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars(t('skills.core.item3.desc'), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                            <div class="skill-row">
                                <div class="skill-icon"><i class="bi bi-clipboard-data"></i></div>
                                <div class="skill-info">
                                    <h5><?php echo htmlspecialchars(t('skills.core.item4.title'), ENT_QUOTES, 'UTF-8'); ?></h5>
                                    <p><?php echo htmlspecialchars(t('skills.core.item4.desc'), ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 d-flex">
                    <div class="skills-panel w-100">
                        <h3 class="panel-title"><i class="bi bi-tools"></i> <?php echo htmlspecialchars(t('skills.tools.title'), ENT_QUOTES, 'UTF-8'); ?></h3>
                        <div class="tech-grid">
                            <div class="tech-item"><img src="https://upload.wikimedia.org/wikipedia/commons/c/cf/New_Power_BI_Logo.svg" alt="Power BI"><span>Power BI</span></div>
                            <div class="tech-item">
                                <svg viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M58 12H88V84H58V12Z" fill="#21A366"/>
                                    <path d="M58 12L8 20V76L58 84V12Z" fill="#107C41"/>
                                    <path d="M22 36L30 52L22 68H32L36 58L40 68H50L42 52L50 36H40L36 46L32 36H22Z" fill="white"/>
                                    <path d="M58 28H76V40H58V28Z" fill="#33C481"/>
                                    <path d="M58 44H76V56H58V44Z" fill="#33C481"/>
                                    <path d="M58 60H76V72H58V60Z" fill="#33C481"/>
                                </svg>
                                <span>Excel</span>
                            </div>
                            <div class="tech-item"><img src="https://n8n.io/favicon.ico" alt="n8n"><span>n8n</span></div>
                            <div class="tech-item"><img src="https://upload.wikimedia.org/wikipedia/commons/2/29/Postgresql_elephant.svg" alt="PostgreSQL"><span>PostgreSQL</span></div>
                            <div class="tech-item"><img src="https://www.metabase.com/images/logo.svg" alt="Metabase"><span>Metabase</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="deneyim" class="section-dark">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title" style="font-size: 1.6rem; font-weight: 800; background: linear-gradient(135deg, var(--primary-color), rgba(242, 200, 17, 0.7)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 40px rgba(242, 200, 17, 0.3); margin-bottom: 1rem; letter-spacing: 1px;"><?php echo htmlspecialchars(t('section.career'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
                        
            <div class="career-timeline timeline-collapsed" id="careerTimeline">
                <?php foreach ($timelineYears as $year): ?>
                    <?php $item = $timelineData[$year] ?? null; ?>
                    <?php if (is_array($item)): ?>
                        <div class="timeline-item" data-year="<?php echo (int) $year; ?>">
                            <div class="timeline-node"></div>
                            <div class="timeline-card" onclick="toggleTimelineCard(this)">
                                <div class="timeline-card-header">
                                    <div class="timeline-card-info">
                                        <span class="timeline-year-badge"><?php echo (int) $year; ?></span>
                                        <h4 class="timeline-role"><?php echo htmlspecialchars((string) ($item['role'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></h4>
                                        <div class="timeline-company">
                                            <i class="bi bi-building"></i>
                                            <?php echo htmlspecialchars((string) ($item['company'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                    </div>
                                    <div class="timeline-toggle"><i class="bi bi-chevron-down"></i></div>
                                </div>
                                <div class="timeline-details">
                                    <div class="timeline-period"><?php echo htmlspecialchars((string) ($item['period'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></div>
                                    <div class="timeline-description"><?php echo $item['description'] ?? ''; ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-2">
                <button type="button" class="timeline-showmore btn btn-outline-light btn-sm" aria-expanded="false" aria-controls="careerTimeline">Daha Fazla</button>
            </div>
        </div>
    </section>

    <section id="raporlar" class="section-dark">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title" style="font-size:1.6rem;font-weight:800;background:linear-gradient(135deg,var(--primary-color),rgba(242,200,17,0.7));-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;text-shadow:0 0 40px rgba(242,200,17,0.3);margin-bottom:0.5rem;letter-spacing:1px;">
                    <?php echo htmlspecialchars(t('section.reports'), ENT_QUOTES, 'UTF-8'); ?>
                </h2>
                <p class="text-secondary mb-0" style="font-size:0.9rem;">
                    <?php echo htmlspecialchars(t('section.reports.subtitle'), ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </div>

            <div class="powerbi-wrapper">

                <!-- Sekmeler -->
                <div class="powerbi-tabs-scroll">
                    <ul class="nav powerbi-tabs" id="powerbiTabs" role="tablist">

                        <!-- ─ Sekme 1: İK Analitiği ─────────────────────────────────────────
                             data-images: Rapor sayfalarının görsel yollarını JSON dizisi olarak girin.
                             Tek sayfa  → ["assets/img/reports/hr-1.jpg"]
                             Üç sayfa   → ["assets/img/reports/hr-1.jpg","assets/img/reports/hr-2.jpg","assets/img/reports/hr-3.jpg"]
                             Görsel yok → [] (placeholder gösterilir)
                        -->
                        <li class="nav-item" role="presentation">
                            <button class="powerbi-tab-btn active"
                                    data-images='["assets/img/reports/hr-1.jpg"]'
                                    data-report-url="<?php echo htmlspecialchars($pbiHrUrl, ENT_QUOTES, 'UTF-8'); ?>"
                                    data-report-title="<?php echo htmlspecialchars(t('report.tab1.title'), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-selected="true">
                                <i class="bi bi-people-fill"></i>
                                <?php echo htmlspecialchars(t('report.tab1.title'), ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        </li>

                        <!-- ─ Sekme 2: Satış Analizi ─── -->
                        <li class="nav-item" role="presentation">
                            <button class="powerbi-tab-btn"
                                    data-images='[]'
                                    data-report-url=""
                                    data-report-title="<?php echo htmlspecialchars(t('report.tab2.title'), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-selected="false">
                                <i class="bi bi-graph-up-arrow"></i>
                                <?php echo htmlspecialchars(t('report.tab2.title'), ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        </li>

                        <!-- ─ Sekme 3: Çağrı Merkezi ─── -->
                        <li class="nav-item" role="presentation">
                            <button class="powerbi-tab-btn"
                                    data-images='[]'
                                    data-report-url=""
                                    data-report-title="<?php echo htmlspecialchars(t('report.tab3.title'), ENT_QUOTES, 'UTF-8'); ?>"
                                    aria-selected="false">
                                <i class="bi bi-headset"></i>
                                <?php echo htmlspecialchars(t('report.tab3.title'), ENT_QUOTES, 'UTF-8'); ?>
                            </button>
                        </li>

                    </ul>
                </div>

                <!-- Görsel / Carousel / Placeholder alanı -->
                <div class="powerbi-frame-area">

                    <!-- Carousel JS tarafından buraya oluşturulur -->
                    <div id="powerbiImageContainer" class="powerbi-image-container d-none"></div>

                    <!-- Görseli olmayan sekmeler için placeholder -->
                    <div class="powerbi-placeholder" id="powerbiPlaceholder">
                        <i class="bi bi-tools powerbi-ph-icon"
                           style="font-size:3.5rem;color:rgba(242,200,17,0.4);"></i>
                        <h4 class="powerbi-ph-title" id="powerbiPlaceholderTitle">
                            <?php echo htmlspecialchars(t('report.tab1.title'), ENT_QUOTES, 'UTF-8'); ?>
                        </h4>
                        <p class="powerbi-ph-text"
                           style="margin-top:10px;font-size:1rem;color:rgba(255,255,255,0.7);">
                            <?php echo htmlspecialchars(t('section.reports.placeholder'), ENT_QUOTES, 'UTF-8'); ?>
                        </p>
                    </div>

                </div>

                <!-- Kimlik Bilgileri Çubuğu -->
                <div class="powerbi-credentials-bar d-none" id="powerbiCredBar">
                    <div class="powerbi-cred-hint">
                        <i class="bi bi-key-fill text-warning"></i>
                        <?php echo htmlspecialchars(t('section.reports.credentials.hint'), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <div class="powerbi-cred-fields">
                        <div class="powerbi-cred-item">
                            <span class="powerbi-cred-label">
                                <?php echo htmlspecialchars(t('section.reports.credentials.email_label'), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="powerbi-cred-value"
                                  data-copy-text="<?php echo htmlspecialchars($pbiEmail, ENT_QUOTES, 'UTF-8'); ?>"
                                  onclick="pbiCopyText(this)"
                                  title="Kopyalamak için tıklayın">
                                <?php echo htmlspecialchars($pbiEmail, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                        <div class="powerbi-cred-item">
                            <span class="powerbi-cred-label">
                                <?php echo htmlspecialchars(t('section.reports.credentials.password_label'), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <span class="powerbi-cred-value"
                                  data-copy-text="<?php echo htmlspecialchars($pbiPassword, ENT_QUOTES, 'UTF-8'); ?>"
                                  onclick="pbiCopyText(this)"
                                  title="Kopyalamak için tıklayın">
                                <?php echo htmlspecialchars($pbiPassword, ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </div>
                    </div>
                    <a id="powerbiOpenLink" href="#" target="_blank" rel="noopener"
                       class="btn btn-primary powerbi-open-btn">
                        <i class="bi bi-box-arrow-up-right me-1"></i>
                        <?php echo htmlspecialchars(t('section.reports.view_interactive'), ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </div>

                <!-- Dil notu -->
                <div class="text-center py-2">
                    <small class="text-secondary"
                           style="font-size:0.8rem;letter-spacing:0.5px;opacity:0.7;">
                        <i class="bi bi-info-circle me-1 text-warning"></i>
                        <?php echo htmlspecialchars(t('section.reports.lang_notice'), ENT_QUOTES, 'UTF-8'); ?>
                    </small>
                </div>

            </div>
        </div>
    </section>

    <section id="iletisim">
        <div class="container">
            <div class="text-center mb-3">
                <h2 class="section-title" style="font-size: 1.6rem; font-weight: 800; background: linear-gradient(135deg, var(--primary-color), rgba(242, 200, 17, 0.7)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; text-shadow: 0 0 40px rgba(242, 200, 17, 0.3); margin-bottom: 0; letter-spacing: 1px;"><?php echo htmlspecialchars(t('section.contact'), ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            
            <div class="row justify-content-center">
                <div class="col-lg-9 col-xl-8">
                    <div class="contact-card" style="padding: 2rem 2.5rem;">
                        <form method="POST" action="#iletisim">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-4">
                                        <input type="text" class="form-control" name="name" placeholder="<?php echo htmlspecialchars(t('contact.name'), ENT_QUOTES, 'UTF-8'); ?>" required style="padding: 0.7rem 1rem;">
                                    </div>
                                    <div class="mb-4">
                                        <input type="email" class="form-control" name="email" placeholder="<?php echo htmlspecialchars(t('contact.email'), ENT_QUOTES, 'UTF-8'); ?>" required style="padding: 0.7rem 1rem;">
                                    </div>
                                    <div class="mb-0">
                                        <input type="text" class="form-control" name="subject" placeholder="<?php echo htmlspecialchars(t('contact.subject'), ENT_QUOTES, 'UTF-8'); ?>" style="padding: 0.7rem 1rem;">
                                    </div>
                                </div>
                                <div class="col-md-6 d-flex flex-column">
                                    <div class="mb-3 flex-grow-1">
                                        <textarea class="form-control h-100" name="message" placeholder="<?php echo htmlspecialchars(t('contact.message'), ENT_QUOTES, 'UTF-8'); ?>" required style="min-height: 200px; padding: 0.7rem 1rem;"></textarea>
                                    </div>
                                    <div>
                                        <input type="hidden" name="contact_form" value="1">
                                        <button type="submit" class="btn btn-primary w-100" style="padding: 0.7rem; font-size: 0.95rem;">
                                            <i class="bi bi-send me-2"></i><?php echo htmlspecialchars(t('contact.send'), ENT_QUOTES, 'UTF-8'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <footer style="padding: 0.5rem 0;">
        <div class="container">
            <div class="text-center">
                <p class="text-secondary mb-0" style="font-size: 0.85rem;"><?php echo htmlspecialchars(t('footer.copyright', ['year' => date('Y')]), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>
        </div>
    </footer>

    <div class="modal fade" id="formModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background: linear-gradient(145deg, rgba(32, 35, 45, 0.95), rgba(24, 26, 32, 0.98)); border: 1px solid rgba(242, 200, 17, 0.2); border-radius: 20px;">
                <div class="modal-body text-center p-4">
                    <?php if ($formSuccess): ?>
                        <div class="mb-3"><i class="bi bi-check-circle" style="font-size: 4rem; color: var(--primary-color);"></i></div>
                        <h4 class="mb-3" style="color: var(--primary-color);"><?php echo htmlspecialchars(t('modal.success.title'), ENT_QUOTES, 'UTF-8'); ?></h4>
                    <?php else: ?>
                        <div class="mb-3"><i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #ff6b6b;"></i></div>
                        <h4 class="mb-3" style="color: #ff6b6b;"><?php echo htmlspecialchars(t('modal.error.title'), ENT_QUOTES, 'UTF-8'); ?></h4>
                    <?php endif; ?>
                    <p class="text-secondary mb-4"><?php echo $formMessage; ?></p>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal"><?php echo htmlspecialchars(t('modal.ok'), ENT_QUOTES, 'UTF-8'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($formSubmitted): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new bootstrap.Modal(document.getElementById('formModal')).show();
        });
    </script>
    <?php endif; ?>

    <script src="assets/js/main.js?v=4.0"></script>
</body>
</html>