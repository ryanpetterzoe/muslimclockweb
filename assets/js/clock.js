/* Muslim Clock Web — display logic (v6) */
(function () {
    'use strict';

    const $  = (s, p = document) => p.querySelector(s);
    const $$ = (s, p = document) => p.querySelectorAll(s);
    const pad = (n) => String(n).padStart(2, '0');

    const PRAYER_LABEL_ID = {
        fajr: 'Subuh', dhuhr: 'Dzuhur', asr: 'Ashar',
        maghrib: 'Maghrib', isha: 'Isya'
    };

    /* Bulan Hijri dalam Bahasa Indonesia */
    const HIJRI_MONTHS_ID = [
        'Muharram', 'Safar', "Rabi'ul Awal", "Rabi'ul Akhir",
        'Jumadil Awal', 'Jumadil Akhir', 'Rajab', "Sya'ban",
        'Ramadhan', 'Syawal', "Dzulqa'dah", 'Dzulhijjah'
    ];
    const HIJRI_MAP = {
        muharram: 0, safar: 1,
        rabiulawal: 2, rabi_iawal: 2, rabi_iawwal: 2,
        rabiulakhir: 3, rabi_iiakhir: 3, rabi_iithani: 3,
        jumadalula: 4, jumadaawal: 4, jumadiawal: 4,
        jumadalakhir: 5, jumadaakhir: 5, jumadithani: 5,
        rajab: 6,
        shaban: 7, syaban: 7,
        ramadan: 8, ramadhan: 8,
        shawwal: 9, syawal: 9,
        dhualqadah: 10, dzulqadah: 10, zulkaedah: 10,
        dhualhijjah: 11, dzulhijjah: 11, zulhijah: 11,
    };

    let state = {
        times: {},              // { fajr:'04:30', dhuhr:'12:00', ... }
        triggeredKey: null,     // YYYYMMDD-prayer once triggered
        adzanActive: false,
        iqomahActive: false,
    };

    /* ============= Analog clock (rAF, smooth sweep) ============= */
    function tickAnalog() {
        const now = new Date();
        const ms  = now.getMilliseconds();
        const sec = now.getSeconds() + ms / 1000;
        const min = now.getMinutes() + sec / 60;
        const hr  = (now.getHours() % 12) + min / 60;

        const handS = $('#handS'), handM = $('#handM'), handH = $('#handH');
        if (handH) handH.setAttribute('transform', `rotate(${(hr * 30).toFixed(2)})`);
        if (handM) handM.setAttribute('transform', `rotate(${(min * 6).toFixed(2)})`);
        if (handS) handS.setAttribute('transform', `rotate(${(sec * 6).toFixed(2)})`);

        requestAnimationFrame(tickAnalog);
    }

    /* ============= Digital clock + date ============= */
    function tickDigital() {
        const now = new Date();
        const h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();

        const main = $('#digital');
        if (main) {
            main.innerHTML =
                `${pad(h)}<span class="text-accent">:</span>${pad(m)}` +
                `<span class="text-accent text-2xl align-top ml-1.5">${pad(s)}</span>`;
        }

        const days   = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const greg = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        const gd = $('#greg-date'); if (gd) gd.textContent = greg;

        updateNextCountdown(now);
        checkAdzanTrigger(now);
    }

    /* ============= Hijri date ============= */
    function loadHijri() {
        const el = $('#hij-date');
        if (!el) return;
        try {
            const parts = new Intl.DateTimeFormat('en-u-ca-islamic-umalqura', {
                day: 'numeric', month: 'long', year: 'numeric'
            }).formatToParts(new Date());
            let day = '', monthName = '', year = '';
            for (const p of parts) {
                if (p.type === 'day')   day = p.value;
                if (p.type === 'month') monthName = p.value;
                if (p.type === 'year')  year = p.value.replace(/\D/g, '');
            }
            const key = monthName.toLowerCase().replace(/['‘’`\s\-_.]/g, '');
            let idx = HIJRI_MAP[key];
            if (idx === undefined) {
                for (const [m, i] of Object.entries(HIJRI_MAP)) {
                    if (key.includes(m)) { idx = i; break; }
                }
            }
            const monthId = (idx !== undefined) ? HIJRI_MONTHS_ID[idx] : monthName;
            el.textContent = `${day} ${monthId} ${year} H`;
        } catch (e) { el.textContent = ''; }
    }

    /* ============= Prayer times ============= */
    async function loadPrayerTimes() {
        try {
            const r = await fetch('api/prayer.php?d=today', { cache: 'no-store' });
            const data = await r.json();
            if (data && data.timings) {
                state.times = normalizeTimes(data.timings);
                renderTimes();
            }
        } catch (e) { console.error('prayer load failed', e); }
    }
    function normalizeTimes(t) {
        const out = {};
        ['Fajr','Sunrise','Dhuhr','Asr','Maghrib','Isha'].forEach(k => {
            if (t[k]) out[k.toLowerCase()] = t[k].split(' ')[0];
        });
        return out;
    }
    function renderTimes() {
        const order = ['fajr','sunrise','dhuhr','asr','maghrib','isha'];
        const now = new Date();
        const nowMin = now.getHours() * 60 + now.getMinutes();
        let nextKey = null, bestDiff = Infinity;
        order.forEach(k => {
            const t = state.times[k];
            const card = $(`.prayer[data-key="${k}"]`);
            if (!card) return;
            card.classList.remove('next');
            const tEl = card.querySelector('[data-time]');
            if (tEl) tEl.textContent = t || '--:--';
            if (!t || k === 'sunrise') return;
            const [hh, mm] = t.split(':').map(Number);
            const m = hh * 60 + mm;
            const diff = m - nowMin;
            if (diff > 0 && diff < bestDiff) { bestDiff = diff; nextKey = k; }
        });
        if (!nextKey) nextKey = 'fajr';
        const next = $(`.prayer[data-key="${nextKey}"]`);
        if (next) next.classList.add('next');
    }

    function updateNextCountdown(now) {
        const order = ['fajr','dhuhr','asr','maghrib','isha'];
        const nowSec = now.getHours()*3600 + now.getMinutes()*60 + now.getSeconds();
        let next = null, bestDiff = Infinity;
        order.forEach(k => {
            if (!state.times[k]) return;
            const [hh, mm] = state.times[k].split(':').map(Number);
            const sec = hh*3600 + mm*60;
            const d = sec - nowSec;
            if (d > 0 && d < bestDiff) { bestDiff = d; next = k; }
        });
        if (!next && state.times.fajr) {
            const [hh, mm] = state.times.fajr.split(':').map(Number);
            bestDiff = (24*3600 - nowSec) + (hh*3600 + mm*60);
            next = 'fajr';
        }
        if (next && bestDiff !== Infinity) {
            const lbl = $('#nextLabel'), cd = $('#nextCountdown');
            if (lbl) lbl.textContent = PRAYER_LABEL_ID[next] || next;
            if (cd) {
                const h = Math.floor(bestDiff / 3600);
                const m = Math.floor((bestDiff % 3600) / 60);
                const s = bestDiff % 60;
                cd.textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
            }
        }
    }

    /* ============= Adzan overlay (reliable trigger) =============
       Trigger ketika "minutes since midnight" lewat dari waktu sholat
       dalam window 60 detik, dan belum pernah ditrigger hari ini. */
    function dateKey(d) {
        return `${d.getFullYear()}${pad(d.getMonth()+1)}${pad(d.getDate())}`;
    }
    function loadTriggered() {
        try {
            return JSON.parse(localStorage.getItem('mc_triggered') || '{}');
        } catch { return {}; }
    }
    function saveTriggered(obj) {
        try { localStorage.setItem('mc_triggered', JSON.stringify(obj)); } catch {}
    }

    function checkAdzanTrigger(now) {
        if (state.adzanActive || state.iqomahActive) return;
        const dKey = dateKey(now);
        const triggered = loadTriggered();
        // bersihkan key hari sebelumnya
        Object.keys(triggered).forEach(k => {
            if (!k.startsWith(dKey)) delete triggered[k];
        });
        const nowSec = now.getHours()*3600 + now.getMinutes()*60 + now.getSeconds();
        const triggerable = ['fajr','dhuhr','asr','maghrib','isha'];
        for (const k of triggerable) {
            const t = state.times[k];
            if (!t) continue;
            const [hh, mm] = t.split(':').map(Number);
            const targetSec = hh*3600 + mm*60;
            const diff = nowSec - targetSec; // positif = sudah lewat
            const key = `${dKey}-${k}`;
            // window 0..60 detik setelah waktu sholat, dan belum ditrigger
            if (diff >= 0 && diff < 60 && !triggered[key]) {
                triggered[key] = true;
                saveTriggered(triggered);
                showAdzan(k);
                break;
            }
        }
    }

    function showAdzan(key) {
        state.adzanActive = true;
        const overlay = $('#adzanOverlay');
        const dur   = parseInt(document.body.dataset.adzanDur  || '600', 10);
        const iqDur = parseInt(document.body.dataset.iqomahDur || '600', 10);
        $('#ovPrayer').textContent = (PRAYER_LABEL_ID[key] || key).toUpperCase();
        $('#ovSub').textContent = 'BERLANGSUNG';
        overlay.classList.remove('hidden');

        startCountdown(dur, () => {
            state.adzanActive = false;
            state.iqomahActive = true;
            $('#ovSub').textContent = 'IQOMAH';
            startCountdown(iqDur, () => {
                state.iqomahActive = false;
                overlay.classList.add('hidden');
            });
        });
    }

    function hideAdzan() {
        state.adzanActive = false;
        state.iqomahActive = false;
        $('#adzanOverlay').classList.add('hidden');
    }

    function startCountdown(seconds, onDone) {
        const el = $('#ovCount');
        let s = seconds;
        const render = () => {
            const m = Math.floor(s / 60), r = s % 60;
            el.textContent = pad(m) + ':' + pad(r);
        };
        render();
        const t = setInterval(() => {
            s--;
            if (s <= 0) { clearInterval(t); render(); onDone && onDone(); return; }
            render();
        }, 1000);
    }

    /* ============= Slideshow ============= */
    function startSlideshow() {
        const slides = $$('#slideshow .slide');
        if (!slides.length) return;

        // Try to play any videos initially (autoplay attribute should already do this)
        slides.forEach(s => {
            if (s.tagName === 'VIDEO') {
                s.muted = true; // ensure muted (browser autoplay policy)
                s.play().catch(() => {
                    // autoplay blocked; will retry on first user interaction
                });
            }
        });
        // Retry on first user interaction (for browsers blocking autoplay)
        const retry = () => {
            slides.forEach(s => { if (s.tagName === 'VIDEO') s.play().catch(()=>{}); });
            document.removeEventListener('click', retry);
            document.removeEventListener('keydown', retry);
        };
        document.addEventListener('click', retry, { once: true });
        document.addEventListener('keydown', retry, { once: true });

        if (slides.length <= 1) return;
        let i = 0;
        setInterval(() => {
            const cur = slides[i];
            cur.classList.remove('active');
            if (cur.tagName === 'VIDEO') cur.pause();
            i = (i + 1) % slides.length;
            const next = slides[i];
            next.classList.add('active');
            if (next.tagName === 'VIDEO') {
                next.currentTime = 0;
                next.play().catch(()=>{});
            }
        }, 8000);
    }

    /* ============= Quran rotation ============= */
    let _quranTypeTimer = null;
    let _quranSlideTimer = null;
    let _quranCounter = 0;

    /**
     * Auto-fit text into a container by shrinking font-size until it fits without overflow.
     * Pastikan tulisan ga kepotong meski ayat panjang.
     */
    function autoFitText(el, opts) {
        if (!el) return;
        const minSize = opts.min || 12;
        const maxSize = opts.max || 30;
        const maxHeight = opts.maxHeight || el.clientHeight || 80;

        // Reset to max first
        el.style.fontSize = maxSize + 'px';
        el.style.whiteSpace = 'normal';

        // Binary search for best fit
        let lo = minSize, hi = maxSize, best = minSize;
        while (lo <= hi) {
            const mid = (lo + hi) >> 1;
            el.style.fontSize = mid + 'px';
            // height check
            if (el.scrollHeight <= maxHeight + 2 && el.scrollWidth <= el.clientWidth + 2) {
                best = mid;
                lo = mid + 1;
            } else {
                hi = mid - 1;
            }
        }
        el.style.fontSize = best + 'px';
    }

    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val || '';
    }

    function applyQuranBasic(data) {
        setText('quranArab',   data.arabic);
        setText('quranTrans',  data.translation || '');
        setText('quranRef',    data.reference   || '');
        setText('quranArab2',  data.arabic);
        setText('quranTrans2', data.translation || '');
        setText('quranRef2',   data.reference   || '');
    }

    function renderQuranFullCard(data) {
        // Set text first then auto-fit so text never gets cut off.
        applyQuranBasic(data);
        const card = document.getElementById('quranCard');
        if (card) {
            card.style.transition = 'opacity .4s ease';
            card.style.opacity = '0';
            setTimeout(() => {
                const arab = document.getElementById('quranArab');
                const trans = document.getElementById('quranTrans');
                if (arab)  autoFitText(arab,  { min: 14, max: 32, maxHeight: 90 });
                if (trans) autoFitText(trans, { min: 11, max: 18, maxHeight: 56 });
                // Counter / progress
                _quranCounter++;
                const prog = document.getElementById('quranProgress');
                if (prog) prog.textContent = '#' + _quranCounter;
                card.style.opacity = '1';
            }, 380);
        }
    }

    function renderQuranSlide(data) {
        const card = document.getElementById('quranCard');
        if (!card) { applyQuranBasic(data); return; }
        // Slide out to the left
        card.style.transition = 'transform .55s ease, opacity .45s ease';
        card.style.transform = 'translateX(-30%)';
        card.style.opacity = '0';
        setTimeout(() => {
            applyQuranBasic(data);
            const arab = document.getElementById('quranArab');
            if (arab) autoFitText(arab, { min: 16, max: 32, maxHeight: 60 });
            // Reset position to right then animate in
            card.style.transition = 'none';
            card.style.transform = 'translateX(30%)';
            card.style.opacity = '0';
            // Force reflow
            void card.offsetWidth;
            card.style.transition = 'transform .6s ease, opacity .6s ease';
            card.style.transform = 'translateX(0)';
            card.style.opacity = '1';
        }, 500);
    }

    function renderQuranTypewriter(data) {
        const arabEl  = document.getElementById('quranArab');
        const transEl = document.getElementById('quranTrans');
        const refEl   = document.getElementById('quranRef');
        const cArab   = document.querySelector('[data-cursor-arab]');
        const cTrans  = document.querySelector('[data-cursor-trans]');

        if (_quranTypeTimer) { clearInterval(_quranTypeTimer); _quranTypeTimer = null; }

        if (refEl)  refEl.textContent  = data.reference || '';
        if (arabEl) arabEl.textContent = '';
        if (transEl) transEl.textContent = '';
        if (cArab)  cArab.style.display  = 'inline';
        if (cTrans) cTrans.style.display = 'none';

        // Mirror to ghost spans for any layout that referenced them
        setText('quranArab2',  data.arabic);
        setText('quranTrans2', data.translation || '');
        setText('quranRef2',   data.reference   || '');

        const arabFull  = data.arabic || '';
        const transFull = data.translation || '';
        const arabChars  = Array.from(arabFull);   // unicode-safe
        const transChars = Array.from(transFull);
        let i = 0, j = 0, phase = 'arab';

        const speed = 55; // ms per char
        _quranTypeTimer = setInterval(() => {
            if (phase === 'arab') {
                if (i < arabChars.length) {
                    arabEl.textContent = arabChars.slice(0, ++i).join('');
                } else {
                    if (cArab)  cArab.style.display  = 'none';
                    if (cTrans) cTrans.style.display = 'inline';
                    phase = 'trans';
                }
            } else if (phase === 'trans') {
                if (j < transChars.length) {
                    transEl.textContent = transChars.slice(0, ++j).join('');
                } else {
                    clearInterval(_quranTypeTimer);
                    _quranTypeTimer = null;
                    if (cTrans) cTrans.style.display = 'none';
                }
            }
        }, speed);
    }

    async function loadQuran() {
        try {
            const r = await fetch('api/quran.php', { cache: 'no-store' });
            const data = await r.json();
            if (!data || !data.arabic) return;

            const mode = document.body.dataset.quranDisplay || 'marquee';

            switch (mode) {
                case 'fullcard':
                    renderQuranFullCard(data);
                    break;
                case 'slide':
                    renderQuranSlide(data);
                    break;
                case 'typewriter':
                    renderQuranTypewriter(data);
                    break;
                case 'card': {
                    const card = document.getElementById('quranCard');
                    if (card) {
                        card.style.transition = 'opacity .4s ease';
                        card.style.opacity = '0';
                        setTimeout(() => { applyQuranBasic(data); card.style.opacity = '1'; }, 400);
                    } else {
                        applyQuranBasic(data);
                    }
                    break;
                }
                default:
                    applyQuranBasic(data);
            }
        } catch(e){ /* ignore */ }
    }

    // Re-fit auto-fit text on resize
    window.addEventListener('resize', () => {
        const mode = document.body.dataset.quranDisplay;
        if (mode === 'fullcard') {
            const arab = document.getElementById('quranArab');
            const trans = document.getElementById('quranTrans');
            if (arab)  autoFitText(arab,  { min: 14, max: 32, maxHeight: 90 });
            if (trans) autoFitText(trans, { min: 11, max: 18, maxHeight: 56 });
        }
    });

    /* ============= Component visibility (driven by body data-* attrs) ============= */
    function applyComponentVisibility() {
        const ds = document.body.dataset;
        // Countdown block: hide both label "Menuju ..." and the value spans/divs.
        if (ds.showCountdown === '0') {
            ['#nextLabel', '#nextCountdown'].forEach(sel => {
                document.querySelectorAll(sel).forEach(el => {
                    el.style.display = 'none';
                    // also hide adjacent "Menuju" labels (the previous siblings before nextLabel)
                });
            });
            // Hide any label text containing "Menuju" near the countdown
            document.querySelectorAll('span, div').forEach(el => {
                const txt = (el.textContent || '').trim();
                if (/^Menuju( Sholat)?$/i.test(txt)) el.style.display = 'none';
            });
        }
        // Hide imam labels (in case PHP-level guard missed any)
        if (ds.showImam === '0') {
            document.querySelectorAll('div').forEach(el => {
                const txt = (el.textContent || '').trim();
                if (txt.startsWith('Imam:')) el.style.display = 'none';
            });
        }
    }

    /* ============= init ============= */
    function init() {
        applyComponentVisibility();
        tickDigital();
        setInterval(tickDigital, 1000);
        requestAnimationFrame(tickAnalog);

        loadHijri();
        loadPrayerTimes();
        setInterval(loadPrayerTimes, 60 * 60 * 1000);

        startSlideshow();
        loadQuran();
        // Interval bergantung mode: typewriter & slide butuh waktu lebih lama
        const qm = document.body.dataset.quranDisplay || 'marquee';
        const quranInterval =
            qm === 'typewriter' ? 45 * 1000 :
            qm === 'slide'      ? 12 * 1000 :
            qm === 'fullcard'   ? 20 * 1000 :
                                  30 * 1000;
        setInterval(loadQuran, quranInterval);

        // Tengah malam → reload data
        setInterval(() => {
            const n = new Date();
            if (n.getHours() === 0 && n.getMinutes() === 0 && n.getSeconds() < 5) {
                loadPrayerTimes(); loadHijri();
            }
        }, 4000);

        // Fullscreen on double-click
        document.addEventListener('dblclick', () => {
            const el = document.documentElement;
            if (!document.fullscreenElement) el.requestFullscreen && el.requestFullscreen();
            else document.exitFullscreen && document.exitFullscreen();
        });

        // Test hotkey: tekan "T" untuk simulasi overlay adzan
        document.addEventListener('keydown', (e) => {
            if (e.key === 't' || e.key === 'T') {
                if (!state.adzanActive && !state.iqomahActive) showAdzan('maghrib');
            }
            if (e.key === 'Escape') hideAdzan();
        });

        // Tombol Test Adzan (visible)
        const testBtn = $('#testAdzan');
        if (testBtn) {
            testBtn.addEventListener('click', () => {
                if (state.adzanActive || state.iqomahActive) hideAdzan();
                else showAdzan('maghrib');
            });
        }

        // Confirm asset version loaded
        console.log('[MuslimClock] build:', window.MC_BUILD || '?', 'clock.js v6+');
    }

    document.addEventListener('DOMContentLoaded', init);
})();
