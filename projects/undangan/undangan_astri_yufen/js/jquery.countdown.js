/* =========================================
   JQUERY COUNTDOWN PLUGIN (KUSTOM)
   Untuk Undangan Astri & Yufen
========================================= */

(function($) {
    $.fn.countdown = function(options) {
        var settings = $.extend({
            date: null,
            offset: null,
            day: 'Hari',
            days: 'Hari'
        }, options);

        if (!settings.date) {
            $.error('Tanggal belum diatur.');
        }

        var container = this;

        // Fungsi penyesuaian zona waktu
        var currentDate = function () {
            var date = new Date();
            var utc = date.getTime() + (date.getTimezoneOffset() * 60000);
            var new_date = new Date(utc + (3600000 * settings.offset));
            return new_date;
        };

        function hitung() {
            var target_date = new Date(settings.date);
            var current_date = currentDate();
            var difference = target_date - current_date;

            // Jika waktu sudah lewat
            if (difference < 0) {
                clearInterval(interval);
                container.html('<div style="font-size: 1.5rem; color: var(--warna-gold); font-family: \'Cinzel\', serif;">Acara Telah Dimulai / Selesai</div>');
                return;
            }

            var days = Math.floor(difference / (1000 * 60 * 60 * 24));
            var hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((difference % (1000 * 60)) / 1000);

            // Tampilan UI Hitung Mundur
            var html = `
                <div style="display: flex; justify-content: center; gap: 15px; text-align: center; font-family: 'Cinzel', serif;">
                    <div style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--warna-gold); padding: 10px; border-radius: 10px; min-width: 70px;">
                        <span style="font-size: 2rem; color: var(--warna-gold); font-weight: bold;">${days}</span><br>
                        <span style="font-size: 0.8rem;">${settings.days}</span>
                    </div>
                    <div style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--warna-gold); padding: 10px; border-radius: 10px; min-width: 70px;">
                        <span style="font-size: 2rem; color: var(--warna-gold); font-weight: bold;">${String(hours).padStart(2, '0')}</span><br>
                        <span style="font-size: 0.8rem;">Jam</span>
                    </div>
                    <div style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--warna-gold); padding: 10px; border-radius: 10px; min-width: 70px;">
                        <span style="font-size: 2rem; color: var(--warna-gold); font-weight: bold;">${String(minutes).padStart(2, '0')}</span><br>
                        <span style="font-size: 0.8rem;">Menit</span>
                    </div>
                    <div style="background: rgba(197, 160, 89, 0.1); border: 1px solid var(--warna-gold); padding: 10px; border-radius: 10px; min-width: 70px;">
                        <span style="font-size: 2rem; color: var(--warna-gold); font-weight: bold;">${String(seconds).padStart(2, '0')}</span><br>
                        <span style="font-size: 0.8rem;">Detik</span>
                    </div>
                </div>
            `;
            container.html(html);
        }

        var interval = setInterval(hitung, 1000);
    };
})(jQuery);