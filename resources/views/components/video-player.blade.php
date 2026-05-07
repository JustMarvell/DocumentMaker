{{--
    Usage:
    @include('components.video-player', [
        'src'    => 'videos/tutorial-01.mp4',
        'title'  => 'Cara Menambah Template',
        'poster' => 'videos/thumbnail-01.jpg',  // optional
    ])
--}}
@props([
    'src' => '',
    'title' => '',
    'poster' => '',
])

@php static $vpIndex = 0;
$vpIndex++;
$id = 'vp-' . $vpIndex; @endphp

<div class="sipadu-video-player" id="{{ $id }}" data-vp>
    <div class="vp-title-bar">
        <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <span>{{ $title }}</span>
    </div>

    <div class="vp-wrap">
        <video class="vp-video"
               @if($poster) poster="{{ route("admin.guide.asset", "test.png") }}" @endif
               preload="metadata">  
            <source src="{{ route("admin.guide.asset", "test1.mp4") }}" type="video/mp4">
            Browser Anda tidak mendukung video HTML5.
        </video>

        {{-- Buffering spinner --}}
        <div class="vp-buffering" data-buffering style="display:none;">
            <div class="vp-spin"></div>
        </div>

        {{-- Big play/pause overlay --}}
        <div class="vp-overlay" data-overlay>
            <button class="vp-big-play" data-play-btn aria-label="Play">
                <svg class="icon-big-play" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                <svg class="icon-big-pause hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
            </button>
        </div>

        {{-- Controls --}}
        <div class="vp-controls" data-controls>

            {{-- Progress bar --}}
            <div class="vp-progress-wrap" data-progress-wrap>
                <div class="vp-buffered" data-buffered></div>
                <div class="vp-played"   data-played></div>
                <input type="range" class="vp-seek" data-seek
                       min="0" max="100" step="0.01" value="0" aria-label="Seek">
                <div class="vp-tooltip"  data-tooltip></div>
            </div>

            <div class="vp-bottom">
                {{-- Left controls --}}
                <div class="vp-left">
                    <button class="vp-btn" data-play-pause aria-label="Play/Pause">
                        <svg class="icon-play" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                        <svg class="icon-pause hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/></svg>
                    </button>
                    <button class="vp-btn" data-rewind aria-label="Rewind 10s" title="-10s">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 5V1L7 6l5 5V7c3.31 0 6 2.69 6 6s-2.69 6-6 6-6-2.69-6-6H4c0 4.42 3.58 8 8 8s8-3.58 8-8-3.58-8-8-8z"/>
                            <text x="7.5" y="15.5" font-size="5" font-weight="bold" fill="currentColor">10</text>
                        </svg>
                    </button>
                    <button class="vp-btn" data-skip aria-label="Skip 10s" title="+10s">
                        <svg fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 5V1l5 5-5 5V7c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6h2c0 4.42-3.58 8-8 8s-8-3.58-8-8 3.58-8 8-8z"/>
                            <text x="7.5" y="15.5" font-size="5" font-weight="bold" fill="currentColor">10</text>
                        </svg>
                    </button>
                    <div class="vp-volume-wrap">
                        <button class="vp-btn" data-mute aria-label="Mute">
                            <svg class="icon-vol-on" fill="currentColor" viewBox="0 0 24 24"><path d="M3 9v6h4l5 5V4L7 9H3zm13.5 3A4.5 4.5 0 0014 7.97v8.05c1.48-.73 2.5-2.25 2.5-4.02z"/></svg>
                            <svg class="icon-vol-off hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M16.5 12A4.5 4.5 0 0014 7.97v2.21l2.45 2.45c.03-.2.05-.41.05-.63zm2.5 0c0 .94-.2 1.82-.54 2.64l1.51 1.51C20.63 14.91 21 13.5 21 12c0-4.28-2.99-7.86-7-8.77v2.06c2.89.86 5 3.54 5 6.71zM4.27 3L3 4.27 7.73 9H3v6h4l5 5v-6.73l4.25 4.25c-.67.52-1.42.93-2.25 1.18v2.06A8.99 8.99 0 0019 20.73L20.73 22 22 20.73 5.27 4 4.27 3zM12 4L9.91 6.09 12 8.18V4z"/></svg>
                        </button>
                        <input type="range" class="vp-volume" data-volume
                               min="0" max="1" step="0.02" value="1" aria-label="Volume">
                    </div>
                    <span class="vp-time" data-time>0:00 / 0:00</span>
                </div>

                {{-- Right controls --}}
                <div class="vp-right">
                    <select class="vp-speed" data-speed aria-label="Playback speed">
                        <option value="0.5">0.5×</option>
                        <option value="0.75">0.75×</option>
                        <option value="1" selected>1×</option>
                        <option value="1.25">1.25×</option>
                        <option value="1.5">1.5×</option>
                        <option value="2">2×</option>
                    </select>
                    <button class="vp-btn" data-fullscreen aria-label="Fullscreen">
                        <svg class="icon-fs-enter" fill="currentColor" viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"/></svg>
                        <svg class="icon-fs-exit hidden" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16h3v3h2v-5H5v2zm3-8H5v2h5V5H8v3zm6 11h2v-3h3v-2h-5v5zm2-11V5h-2v5h5V8h-3z"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>