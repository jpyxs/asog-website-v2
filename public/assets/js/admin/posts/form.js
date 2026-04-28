(function () {
    /* ── Cover image upload ──────────────── */
    var zone = document.getElementById('uploadZone');
    var input = document.getElementById('imageInput');
    var preview = document.getElementById('uploadPreview');
    var label = document.getElementById('uploadLabel');

    if (zone && input) {
        if (preview && preview.querySelector('img')) {
            label.style.display = 'none';
        }

        zone.addEventListener('click', function (e) {
            if (e.target === input) return;
            input.click();
        });

        input.addEventListener('change', function () {
            var file = this.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="">';
                label.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        zone.addEventListener('dragover', function (e) {
            e.preventDefault();
            zone.style.borderColor = '#03558C';
            zone.style.background = '#fafcff';
        });
        zone.addEventListener('dragleave', function () {
            zone.style.borderColor = '';
            zone.style.background = '';
        });
        zone.addEventListener('drop', function (e) {
            e.preventDefault();
            zone.style.borderColor = '';
            zone.style.background = '';
            var files = e.dataTransfer.files;
            if (files.length > 0 && files[0].type.startsWith('image/')) {
                input.files = files;
                input.dispatchEvent(new Event('change'));
            }
        });
    }
})();
