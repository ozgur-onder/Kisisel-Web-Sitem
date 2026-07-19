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
// POWER BI RAPORLAR — SEKME & CAROUSEL YÖNETİMİ
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    const tabs           = document.querySelectorAll('.powerbi-tab-btn');
    const placeholder    = document.getElementById('powerbiPlaceholder');
    const phTitle        = document.getElementById('powerbiPlaceholderTitle');
    const imageContainer = document.getElementById('powerbiImageContainer');
    const credBar        = document.getElementById('powerbiCredBar');
    const openLink       = document.getElementById('powerbiOpenLink');
    const frameArea      = document.querySelector('.powerbi-frame-area');

    if (!tabs.length) return;

    function activateTab(btn) {
        tabs.forEach(t => { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-selected', 'true');

        const reportUrl = (btn.dataset.reportUrl   || '').trim();
        const title     = (btn.dataset.reportTitle || '').trim();

        // data-images: JSON dizisi → ["img1.jpg","img2.jpg",...]
        let images = [];
        try { images = JSON.parse(btn.dataset.images || '[]'); } catch(e) { images = []; }
        images = images.filter(Boolean);

        if (phTitle) phTitle.textContent = title;

        if (images.length > 0) {
            // Görseller var → carousel yap
            if (placeholder)    placeholder.style.display = 'none';
            if (imageContainer) {
                imageContainer.classList.remove('d-none');
                buildCarousel(imageContainer, images, reportUrl, title);
            }
            if (frameArea) frameArea.classList.add('has-image');
            if (credBar)   credBar.classList.remove('d-none');
            if (openLink)  openLink.href = reportUrl || '#';
        } else {
            // Görsel yok → placeholder
            if (imageContainer) imageContainer.classList.add('d-none');
            if (frameArea)      frameArea.classList.remove('has-image');
            if (credBar)        credBar.classList.add('d-none');
            if (placeholder)    placeholder.style.display = '';
        }
    }

    tabs.forEach(btn => btn.addEventListener('click', function () { activateTab(this); }));
    if (tabs[0]) activateTab(tabs[0]);
});

// ── Carousel: HTML oluştur ─────────────────────────────────
function buildCarousel(container, images, reportUrl, title) {
    const multi = images.length > 1;
    const safeUrl = reportUrl ? reportUrl.replace(/'/g, "\\'") : '';
    const clickAttr = reportUrl
        ? `onclick="window.open('${safeUrl}','_blank','noopener')" style="cursor:pointer"`
        : '';

    let html = `<div class="pbi-carousel" data-current="0">`;

    // Sayaç (sadece çoklu görselde)
    if (multi) {
        html += `<div class="pbi-carousel-counter">
                    <span class="pbi-current">1</span> / ${images.length}
                 </div>`;
    }

    // Slayt track
    html += `<div class="pbi-carousel-track">`;
    images.forEach((src, i) => {
        html += `<div class="pbi-slide">
                    <img src="${src}"
                         alt="${title} – Sayfa ${i + 1}"
                         loading="${i === 0 ? 'eager' : 'lazy'}"
                         ${clickAttr}>
                 </div>`;
    });
    html += `</div>`;

    // İleri / Geri butonları + Noktalar (sadece çoklu görselde)
    if (multi) {
        html += `<button class="pbi-carousel-btn pbi-prev"
                         onclick="pbiMove(this,-1)"
                         aria-label="Önceki sayfa">
                    <i class="bi bi-chevron-left"></i>
                 </button>
                 <button class="pbi-carousel-btn pbi-next"
                         onclick="pbiMove(this,1)"
                         aria-label="Sonraki sayfa">
                    <i class="bi bi-chevron-right"></i>
                 </button>
                 <div class="pbi-dots">`;
        images.forEach((_, i) => {
            html += `<button class="pbi-dot${i === 0 ? ' active' : ''}"
                              onclick="pbiGoTo(this.closest('.pbi-carousel'),${i})"
                              aria-label="Sayfa ${i + 1}"></button>`;
        });
        html += `</div>`;
    }

    html += `</div>`; // .pbi-carousel
    container.innerHTML = html;

    // İlk konumu uygula (prev gizli)
    pbiUpdateState(container.querySelector('.pbi-carousel'), 0, images.length);

    // Dokunmatik kaydırma (swipe) desteği
    if (multi) {
        const track = container.querySelector('.pbi-carousel-track');
        let startX = 0;
        track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        track.addEventListener('touchend', e => {
            const diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) < 40) return; // Çok kısa swipe'ı yoksay
            const carousel = container.querySelector('.pbi-carousel');
            const current  = parseInt(carousel.dataset.current || '0');
            const total    = images.length;
            const next     = diff > 0 ? Math.min(current + 1, total - 1) : Math.max(current - 1, 0);
            if (next !== current) pbiGoTo(carousel, next);
        }, { passive: true });
    }
}

// ── Carousel: İleri / Geri butonu ─────────────────────────
function pbiMove(btn, dir) {
    const carousel = btn.closest('.pbi-carousel');
    const total    = carousel.querySelectorAll('.pbi-slide').length;
    const current  = parseInt(carousel.dataset.current || '0');
    const next     = current + dir;
    if (next >= 0 && next < total) pbiGoTo(carousel, next);
}

// ── Carousel: Belirli slayta git ──────────────────────────
function pbiGoTo(carousel, index) {
    pbiUpdateState(carousel, index, carousel.querySelectorAll('.pbi-slide').length);
}

// ── Carousel: Durumu güncelle ──────────────────────────────
function pbiUpdateState(carousel, index, total) {
    carousel.dataset.current = index;

    // Track'i kaydır
    const track = carousel.querySelector('.pbi-carousel-track');
    if (track) track.style.transform = `translateX(-${index * 100}%)`;

    // Sayaç
    const counter = carousel.querySelector('.pbi-current');
    if (counter) counter.textContent = index + 1;

    // Noktalar
    carousel.querySelectorAll('.pbi-dot').forEach((dot, i) => {
        dot.classList.toggle('active', i === index);
    });

    // Prev / Next görünürlüğü (doğrusal navigasyon)
    const prevBtn = carousel.querySelector('.pbi-prev');
    const nextBtn = carousel.querySelector('.pbi-next');
    if (prevBtn) prevBtn.style.display = index === 0           ? 'none' : '';
    if (nextBtn) nextBtn.style.display = index === total - 1   ? 'none' : '';
}

// ── Panoya kopyalama (kimlik bilgileri için) ───────────────
function pbiCopyText(el) {
    const text     = (el.dataset.copyText || el.textContent).trim();
    const original = el.textContent.trim();
    navigator.clipboard.writeText(text).then(() => {
        el.textContent = '✓';
        el.style.cssText = 'background:rgba(72,199,116,0.2);border-color:rgba(72,199,116,0.4);color:#48c774';
        setTimeout(() => { el.textContent = original; el.style.cssText = ''; }, 1500);
    }).catch(() => {
        try {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;opacity:0';
            document.body.appendChild(ta);
            ta.focus(); ta.select();
            document.execCommand('copy');
            document.body.removeChild(ta);
            el.textContent = '✓';
            setTimeout(() => { el.textContent = original; }, 1500);
        } catch(e) {}
    });
}