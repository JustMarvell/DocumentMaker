(function () {
    function fmt(s) {
        s = Math.floor(s || 0);
        const m = Math.floor(s / 60), sec = s % 60;
        return m + ':' + String(sec).padStart(2, '0');
    }

    function initPlayer(root) {
        const video = root.querySelector('.vp-video');
        const overlay = root.querySelector('[data-overlay]');
        const bigPlay = root.querySelector('[data-play-btn]');
        const controls = root.querySelector('[data-controls]');
        const ppBtn = root.querySelector('[data-play-pause]');
        const rewind = root.querySelector('[data-rewind]');
        const skip = root.querySelector('[data-skip]');
        const mute = root.querySelector('[data-mute]');
        const volSlider = root.querySelector('[data-volume]');
        const timeEl = root.querySelector('[data-time]');
        const seek = root.querySelector('[data-seek]');
        const buffered = root.querySelector('[data-buffered]');
        const played = root.querySelector('[data-played]');
        const tooltip = root.querySelector('[data-tooltip]');
        const speed = root.querySelector('[data-speed]');
        const fsBtn = root.querySelector('[data-fullscreen]');
        const wrap = root.querySelector('.vp-wrap');

        // icons helpers
        const iconPlay = ppBtn.querySelector('.icon-play');
        const iconPause = ppBtn.querySelector('.icon-pause');
        const iconVolOn = mute.querySelector('.icon-vol-on');
        const iconVolOff = mute.querySelector('.icon-vol-off');
        const iconFsIn = fsBtn.querySelector('.icon-fs-enter');
        const iconFsOut = fsBtn.querySelector('.icon-fs-exit');

        function setPlayIcons(playing) {
            iconPlay.classList.toggle('hidden', playing);
            iconPause.classList.toggle('hidden', !playing);
            overlay.classList.toggle('hide', playing);
            overlay.classList.toggle('clickable', !playing);
            wrap.classList.toggle('paused', !playing);
        }

        function setVolIcons() {
            const muted = video.muted || video.volume === 0;
            iconVolOn.classList.toggle('hidden', muted);
            iconVolOff.classList.toggle('hidden', !muted);
        }

        // Play / pause
        function togglePlay() {
            if (video.paused) {
                // pause all other players on the page
                document.querySelectorAll('[data-vp] .vp-video').forEach(v => {
                    if (v !== video && !v.paused) v.pause();
                });
                video.play();
            } else {
                video.pause();
            }
        }

        bigPlay.addEventListener('click', togglePlay);
        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) togglePlay();
        });
        ppBtn.addEventListener('click', togglePlay);

        video.addEventListener('play', () => setPlayIcons(true));
        video.addEventListener('pause', () => setPlayIcons(false));
        video.addEventListener('ended', () => setPlayIcons(false));

        // Rewind / Skip
        rewind.addEventListener('click', () => { video.currentTime = Math.max(0, video.currentTime - 10); });
        skip.addEventListener('click', () => { video.currentTime = Math.min(video.duration || 0, video.currentTime + 10); });

        // Volume
        mute.addEventListener('click', () => {
            video.muted = !video.muted;
            if (!video.muted && video.volume === 0) video.volume = 0.5;
            volSlider.value = video.muted ? 0 : video.volume;
            setVolIcons();
        });
        volSlider.addEventListener('input', () => {
            video.volume = parseFloat(volSlider.value);
            video.muted = video.volume === 0;
            setVolIcons();
        });
        setVolIcons();

        // Progress
        video.addEventListener('timeupdate', () => {
            if (!video.duration) return;
            const pct = (video.currentTime / video.duration) * 100;
            played.style.width = pct + '%';
            seek.value = pct;
            timeEl.textContent = fmt(video.currentTime) + ' / ' + fmt(video.duration);
        });

        video.addEventListener('progress', () => {
            if (!video.duration || !video.buffered.length) return;
            const buf = (video.buffered.end(video.buffered.length - 1) / video.duration) * 100;
            buffered.style.width = buf + '%';
        });

        video.addEventListener('loadedmetadata', () => {
            timeEl.textContent = '0:00 / ' + fmt(video.duration);
        });

        // Seek scrubber
        seek.addEventListener('input', () => {
            if (!video.duration) return;
            video.currentTime = (parseFloat(seek.value) / 100) * video.duration;
        });

        // Seek tooltip
        const progressWrap = root.querySelector('[data-progress-wrap]');
        progressWrap.addEventListener('mousemove', (e) => {
            if (!video.duration) return;
            const rect = progressWrap.getBoundingClientRect();
            const pct = Math.min(1, Math.max(0, (e.clientX - rect.left) / rect.width));
            const t = pct * video.duration;
            tooltip.textContent = fmt(t);
            tooltip.style.left = (pct * 100) + '%';
        });

        // Speed
        speed.addEventListener('change', () => { video.playbackRate = parseFloat(speed.value); });

        // Fullscreen
        fsBtn.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                wrap.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        });
        document.addEventListener('fullscreenchange', () => {
            const fs = !!document.fullscreenElement;
            iconFsIn.classList.toggle('hidden', fs);
            iconFsOut.classList.toggle('hidden', !fs);
        });

        // Keyboard shortcuts when focused
        root.setAttribute('tabindex', '0');
        root.addEventListener('keydown', (e) => {
            if (['INPUT', 'SELECT', 'TEXTAREA'].includes(e.target.tagName)) return;
            if (e.key === ' ' || e.key === 'k') { e.preventDefault(); togglePlay(); }
            if (e.key === 'ArrowLeft') { e.preventDefault(); video.currentTime = Math.max(0, video.currentTime - 5); }
            if (e.key === 'ArrowRight') { e.preventDefault(); video.currentTime = Math.min(video.duration || 0, video.currentTime + 5); }
            if (e.key === 'm') { mute.click(); }
            if (e.key === 'f') { fsBtn.click(); }
        });

        // ── Auto-pause on scroll out of view
        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver(
                (entries) => {
                    entries.forEach(entry => {
                        if (!entry.isIntersecting && !video.paused) {
                            video.pause();
                        }
                    });
                },
                { threshold: 0.2 }   // pause when less than 20 % visible
            );
            obs.observe(root);
        }
    }

    // Init all players on page
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-vp]').forEach(initPlayer);
    });
})();