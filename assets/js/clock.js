/* Muslim Clock Web — display logic */
(function () {
    'use strict';

    const $ = (s) => document.querySelector(s);
    const $$ = (s) => document.querySelectorAll(s);

    const PRAYER_KEYS = ['fajr', 'sunrise', 'dhuhr', 'asr', 'maghrib', 'isha'];
    const PRAYER_LABEL_ID = {
        fajr: 'Subuh', sunrise: 'Syuruq', dhuhr: 'Dzuhur',
        asr: 'Ashar', maghrib: 'Maghrib', isha: 'Isya'
    };

    let state = {
        times: {},      // { fajr: 'HH:MM', ... } today
        timesNext: {},  // tomorrow
        triggered: {},  // { 'fajr': true } already triggered today
        date: new Date(),
        adzanActive: false,
        iqomahActive: false,
    };

    /* ------- Clock ticking ------- */
    function tick() {
        const now = new Date();
        state.date = now;

        const h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
        // analog
        const sDeg = s * 6;
        const mDeg = m * 6 + s * 0.1;
        const hDeg = (h % 12) * 30 + m * 0.5;
        const handS = $('#handS'), handM = $('#handM'), handH = $('#handH');
        if (handS) handS.style.transform = `rotate(${sDeg}deg)`;
        if (handM) handM.style.transform = `rotate(${mDeg}deg)`;
        if (handH) handH.style.transform = `rotate(${hDeg}deg)`;
        // digital
        const pad = (n) => String(n).padStart(2, '0');
        $('#digitalClock').textContent = `${pad(h)}:${pad(m)}:${pad(s)}`;
        // greg date Indonesian
        const days = ['Minggu','Senin','Selasa','Rabu','Kamis',"Jum'at",'Sabtu'];
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        const greg = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
        $('#greg-date').textContent = greg;
        $('#ltGreg').textContent = greg + ' • ' + `${pad(h)}:${pad(m)}`;

        // friday => label dhuhr -> jumat
        if (now.getDay() === 5) $('#dhuhrLabel').textContent = 'الجمعة';

        checkAdzanTrigger(now);
    }

    /* ------- Hijri date ------- */
    function loadHijri() {
        try {
            const f = new Intl.DateTimeFormat('id-u-ca-islamic-umalqura', {
                day: 'numeric', month: 'long', year: 'numeric'
            });
            $('#hij-date').textContent = f.format(new Date()) + ' H';
        } catch (e) {
            $('#hij-date').textContent = '';
        }
    }

    /* ------- Prayer times ------- */
    async function loadPrayerTimes() {
        try {
            const r = await fetch('api/prayer.php?d=today', { cache: 'no-store' });
            const data = await r.json();
            if (data && data.timings) {
                state.times = normalizeTimes(data.timings);
                state.triggered = {};
                renderTimes();
            }
        } catch (e) { console.error('prayer load failed', e); }
    }

    function normalizeTimes(t) {
        // Aladhan returns "HH:MM (TZ)" => take HH:MM
        const out = {};
        ['Fajr','Sunrise','Dhuhr','Asr','Maghrib','Isha'].forEach(k => {
            if (t[k]) out[k.toLowerCase()] = t[k].split(' ')[0];
        });
        return out;
    }

    function renderTimes() {
        const order = ['fajr','sunrise','dhuhr','asr','maghrib','isha'];
        const now = state.date;
        const nowMin = now.getHours() * 60 + now.getMinutes();
        let nextKey = null, bestDiff = Infinity;

        order.forEach(k => {
            const t = state.times[k];
            const card = document.querySelector(`.prayer[data-key="${k}"]`);
            if (!card) return;
            card.classList.remove('next');
            card.querySelector('[data-time]').textContent = t || '--:--';
            if (!t || k === 'sunrise') return;
            const [hh, mm] = t.split(':').map(Number);
            const m = hh * 60 + mm;
            const diff = m - nowMin;
            if (diff > 0 && diff < bestDiff) { bestDiff = diff; nextKey = k; }
        });
        if (!nextKey) nextKey = 'fajr'; // tomorrow's fajr
        const next = document.querySelector(`.prayer[data-key="${nextKey}"]`);
        if (next) next.classList.add('next');
    }

    /* ------- Adzan overlay & iqomah countdown ------- */
    function checkAdzanTrigger(now) {
        if (state.adzanActive || state.iqomahActive) return;
        const hh = now.getHours(), mm = now.getMinutes();
        const hhmm = String(hh).padStart(2,'0')+':'+String(mm).padStart(2,'0');
        const triggerable = ['fajr','dhuhr','asr','maghrib','isha'];
        for (const k of triggerable) {
            if (state.times[k] === hhmm && !state.triggered[k] && now.getSeconds() < 5) {
                state.triggered[k] = true;
                showAdzan(k);
                break;
            }
        }
    }

    function showAdzan(key) {
        state.adzanActive = true;
        const overlay = $('#adzanOverlay');
        const dur = parseInt(document.body.dataset.adzanDur || '600', 10);
        const iqDur = parseInt(document.body.dataset.iqomahDur || '600', 10);
        $('#ovPrayer').textContent = (PRAYER_LABEL_ID[key] || key).toUpperCase();
        $('#ovSub').textContent = 'Berlangsung';
        overlay.classList.remove('hidden');

        startCountdown(dur, () => {
            // iqomah phase
            state.adzanActive = false;
            state.iqomahActive = true;
            $('#ovSub').textContent = 'IQOMAH';
            startCountdown(iqDur, () => {
                state.iqomahActive = false;
                overlay.classList.add('hidden');
            });
        });
    }

    function startCountdown(seconds, onDone) {
        const el = $('#ovCount');
        let s = seconds;
        const render = () => {
            const m = Math.floor(s/60), r = s%60;
            el.textContent = String(m).padStart(2,'0')+':'+String(r).padStart(2,'0');
        };
        render();
        const t = setInterval(() => {
            s--;
            if (s <= 0) { clearInterval(t); render(); onDone && onDone(); return; }
            render();
        }, 1000);
    }

    /* ------- Slideshow ------- */
    function startSlideshow() {
        const slides = $$('#slideshow .slide');
        if (slides.length <= 1) return;
        let i = 0;
        // play first video if any
        if (slides[0].tagName === 'VIDEO') slides[0].play().catch(()=>{});
        setInterval(() => {
            slides[i].classList.remove('active');
            if (slides[i].tagName === 'VIDEO') slides[i].pause();
            i = (i + 1) % slides.length;
            slides[i].classList.add('active');
            if (slides[i].tagName === 'VIDEO') slides[i].play().catch(()=>{});
        }, 8000);
    }

    /* ------- Quran rotation ------- */
    async function loadQuran() {
        try {
            const r = await fetch('api/quran.php', { cache: 'no-store' });
            const data = await r.json();
            if (data && data.arabic) {
                $('#quranArab').textContent = data.arabic;
                $('#quranTrans').textContent = data.translation || '';
                $('#quranRef').textContent = data.reference || '';
            }
        } catch(e){ /* ignore */ }
    }

    /* ------- init ------- */
    function init() {
        tick(); setInterval(tick, 1000);
        loadHijri();
        loadPrayerTimes();
        setInterval(loadPrayerTimes, 60 * 60 * 1000); // refresh setiap jam
        startSlideshow();
        loadQuran();
        setInterval(loadQuran, 30 * 1000); // ganti ayat tiap 30 detik
        // Refresh date setiap tengah malam
        setInterval(() => {
            const n = new Date();
            if (n.getHours() === 0 && n.getMinutes() === 0 && n.getSeconds() < 5) {
                loadPrayerTimes(); loadHijri();
            }
        }, 4000);

        // Click anywhere to enter fullscreen
        document.addEventListener('dblclick', () => {
            const el = document.documentElement;
            if (!document.fullscreenElement) el.requestFullscreen && el.requestFullscreen();
            else document.exitFullscreen && document.exitFullscreen();
        });
    }

    document.addEventListener('DOMContentLoaded', init);
})();
