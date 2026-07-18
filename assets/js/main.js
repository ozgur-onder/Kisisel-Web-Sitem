// Tarayıcının kendi scroll restore'unu kapat — JS tam kontrol
if ('scrollRestoration' in history) {
    history.scrollRestoration = 'manual';
}

function setNavHeightCssVar() {
    const navbar = document.querySelector('.navbar');
    if (!navbar) return;
    const h = Math.round(navbar.offsetHeight || 0);
    if (h > 0) {
        document.documentElement.style.setProperty('--nav-h', `${h}px`, 'important');
    }
}

function getNavHeight() {
    const doc = document.documentElement;
    const computed = getComputedStyle(doc).getPropertyValue('--nav-h');
    const parsed = parseInt(computed, 10);
    if (!Number.isNaN(parsed) && parsed > 0) return parsed;
    const navbar = document.querySelector('.navbar');
    return Math.round(navbar ? navbar.offsetHeight : 76);
}

// Navbar offset ile hedef section'a scroll
function scrollToSection(target, instant) {
    if (!target) return;
    const navHeight = getNavHeight();
    let offsetTop = 0;
    let el = target;
    while (el) { offsetTop += el.offsetTop; el = el.offsetParent; }
    const desired = Math.max(offsetTop - navHeight, 0);
    const maxScroll = Math.max(document.documentElement.scrollHeight - window.innerHeight, 0);
    const top = Math.min(desired, maxScroll);
    window.scrollTo({ top, behavior: instant ? 'auto' : 'smooth' });
}

function resolveHashTarget(hash) {
    if (!hash) return null;
    let cleaned = hash.trim();
    if (!cleaned) return null;
    const lastHashIndex = cleaned.lastIndexOf('#');
    if (lastHashIndex >= 0) {
        cleaned = cleaned.slice(lastHashIndex + 1);
    } else if (cleaned.startsWith('#')) {
        cleaned = cleaned.slice(1);
    }
    if (!cleaned) return null;
    return document.getElementById(cleaned);
}

function scrollToHash(hash, instant) {
    const target = resolveHashTarget(hash);
    if (!target) return;
    scrollToSection(target, !!instant);
}

function normalizeHash(input) {
    if (!input) return '';
    let value = input.trim();
    if (!value) return '';
    const lastHashIndex = value.lastIndexOf('#');
    if (lastHashIndex >= 0) {
        value = value.slice(lastHashIndex);
    }
    if (!value.startsWith('#')) {
        value = `#${value}`;
    }
    return value;
}

document.addEventListener('DOMContentLoaded', function() {
    setNavHeightCssVar();
    window.addEventListener('resize', setNavHeightCssVar);
    window.addEventListener('orientationchange', setNavHeightCssVar);

    window.__navOffsetTest = async function() {
        setNavHeightCssVar();
        const navbar = document.querySelector('.navbar');
        const navH = Math.round(navbar ? navbar.offsetHeight : 0);
        const ids = ['#hero', '#hakkimda', '#yetkinlikler', '#deneyim', '#iletisim'];
        const results = [];

        for (const id of ids) {
            const el = document.querySelector(id);
            if (!el) {
                results.push({ id, ok: false, reason: 'not found' });
                continue;
            }
            scrollToSection(el, true);
            await new Promise(r => setTimeout(r, 50));
            const top = Math.round(el.getBoundingClientRect().top);
            const delta = top - navH;
            results.push({ id, top, navH, delta, ok: Math.abs(delta) <= 3 });
        }
        console.table(results);
        return results;
    };

    // Sayfa yüklenince URL'de hash varsa doğru pozisyona scroll
    if (window.location.hash) {
        const hash = window.location.hash;
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                scrollToHash(hash, true);
            });
        });
    }

    // Nav link tıklamalarında navbar offset ile scroll
    const currentPath = window.location.pathname.replace(/\/+$/, '');
    document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href') || '';
            const url = new URL(href, window.location.href);
            const hash = normalizeHash(url.hash || href);
            const targetPath = url.pathname.replace(/\/+$/, '');
            if (!hash || hash === '#') return;
            if (targetPath && targetPath !== currentPath) return; // başka sayfaya gidiyorsa default davranış
            const targetEl = resolveHashTarget(hash);
            if (!targetEl) return;
            e.preventDefault();
            // URL hash'i güncelle (scrollspy/active state için)
            const baseUrl = window.location.pathname + window.location.search;
            history.replaceState(null, '', `${baseUrl}${hash}`);
            scrollToHash(hash, false);

            // Mobil menüyü kapat
            const navCollapse = document.querySelector('.navbar-collapse');
            if (navCollapse && navCollapse.classList.contains('show')) {
                const bsCollapse = bootstrap.Collapse.getInstance(navCollapse);
                if (bsCollapse) bsCollapse.hide();
            }
        });
    });
});

// Navbar (Üst Menü) Kaydırma Efekti
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.style.background = 'rgba(13, 17, 23, 0.98)';
    } else {
        navbar.style.background = 'rgba(13, 17, 23, 0.95)';
    }
});

// Sayfa kaydırıldıkça menüdeki aktif linki (Sarı renk) otomatik değiştirme
const sections = document.querySelectorAll('section[id]');
const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        if (window.scrollY >= sectionTop) {
            current = section.getAttribute('id');
        }
    });
    
    navLinks.forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href') || '';
        const hashIndex = href.indexOf('#');
        const linkHash = hashIndex >= 0 ? href.slice(hashIndex) : '';

        if (linkHash === '#' + current) {
            link.classList.add('active');
        }
    });
});

// Kariyer (Timeline) kartlarına tıklayınca açılıp kapanma animasyonu
function toggleTimelineCard(card) {
    const wasExpanded = card.classList.contains('expanded');
    document.querySelectorAll('.timeline-card.expanded').forEach(c => {
        if (c !== card) c.classList.remove('expanded');
    });
    if (wasExpanded) card.classList.remove('expanded');
    else card.classList.add('expanded');
}

// "Daha Fazla" Butonlarının Akıllı Kontrolü
document.addEventListener('DOMContentLoaded', function() {
    
    // Hakkımda Bölümü
    const aboutBody = document.getElementById('aboutBody');
    const aboutBtn = document.querySelector('.about-readmore');
    if (aboutBody && aboutBtn) {
        // Gerçek yüksekliği ölçmek için önce is-collapsed'ı geçici kaldır
        aboutBody.classList.remove('is-collapsed');
        const fullHeight = aboutBody.scrollHeight;
        const threshold = 200;

        if (fullHeight <= threshold) {
            // İçerik sığıyor: buton gizli, içerik açık
            aboutBtn.style.display = 'none';
        } else {
            // İçerik taşıyor: collapse uygula ve butonu göster
            aboutBody.classList.add('is-collapsed');
            aboutBtn.addEventListener('click', function() {
                const collapsed = aboutBody.classList.contains('is-collapsed');
                aboutBody.classList.toggle('is-collapsed');
                aboutBtn.setAttribute('aria-expanded', collapsed ? 'true' : 'false');
                aboutBtn.textContent = collapsed ? 'Daha Az Göster' : 'Devamını Gör';
            });
        }
    }

    // Deneyim Bölümü
    const careerTimeline = document.getElementById('careerTimeline');
    const careerBtn = document.querySelector('.timeline-showmore');
    if (careerTimeline && careerBtn) {
        const items = careerTimeline.querySelectorAll('.timeline-item');
        // Eğer 3 veya daha az deneyim varsa butonu hiç gösterme
        if (items.length <= 3) {
            careerBtn.style.display = 'none';
            careerTimeline.classList.remove('timeline-collapsed');
        } else {
            careerBtn.addEventListener('click', function() {
                const collapsed = careerTimeline.classList.contains('timeline-collapsed');
                careerTimeline.classList.toggle('timeline-collapsed');
                careerBtn.setAttribute('aria-expanded', collapsed ? 'true' : 'false');
                careerBtn.textContent = collapsed ? 'Daha Az Göster' : 'Daha Fazla';
            });
        }
    }
});
// ============================================================
// POWER BI RAPORLAR SEKME YÖNETİMİ
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const tabs        = document.querySelectorAll('.powerbi-tab-btn');
    const iframe      = document.getElementById('powerbiFrame');
    const placeholder = document.getElementById('powerbiPlaceholder');
    const loading     = document.getElementById('powerbiLoading');
    const footer      = document.getElementById('powerbiFooter');
    const openLink    = document.getElementById('powerbiOpenLink');
    const phTitle     = document.getElementById('powerbiPlaceholderTitle');

    if (!tabs.length || !iframe) return;

    function activateTab(btn) {
        // Aktif sekme stilini güncelle
        tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        const url   = (btn.dataset.embedUrl || '').trim();
        const title = (btn.dataset.reportTitle || '').trim();

        // Placeholder başlığını güncelle
        if (phTitle) phTitle.textContent = title;

        if (!url) {
            // Embed URL boş → placeholder göster
            iframe.classList.add('d-none');
            if (loading) loading.classList.add('d-none');
            if (footer)  footer.classList.add('d-none');
            if (placeholder) placeholder.style.display = '';
            return;
        }

        // Embed URL var → iframe yükle
        if (placeholder) placeholder.style.display = 'none';
        if (loading) loading.classList.remove('d-none');
        iframe.classList.add('d-none');
        if (footer) footer.classList.add('d-none');

        iframe.onload = function () {
    console.log("Iframe yüklendi!"); // Tarayıcı konsolunda (F12) bu yazıyı görüyor musun?
    if (loading) loading.classList.add('d-none');
    iframe.classList.remove('d-none');
    if (footer) footer.classList.remove('d-none');
};

        iframe.src = url;
    }

    tabs.forEach(btn => {
        btn.addEventListener('click', function () { activateTab(this); });
    });

    // İlk sekmeyi başlat
    if (tabs[0]) activateTab(tabs[0]);
});
