document.addEventListener('DOMContentLoaded', function () {
  var bootstrap = document.getElementById('adminQuillBootstrap');
  var appBase = bootstrap ? (bootstrap.getAttribute('data-base-url') || '').replace(/\/$/, '') : '';

  function uploadImage(file) {
    var fd = new FormData();
    fd.append('image', file);

    return fetch(appBase + '/admin/posts/upload-image', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: fd
    })
      .then(function (response) {
        return response.json();
      })
      .then(function (data) {
        if (data.error) {
          alert(data.error);
          return null;
        }
        return data.url;
      })
      .catch(function () {
        alert('Image upload failed.');
        return null;
      });
  }

  function insertImage(quill, url) {
    var range = quill.getSelection(true);
    quill.insertText(range.index, '\n');
    quill.insertEmbed(range.index + 1, 'image', url);
    quill.insertText(range.index + 2, '\n');
    quill.setSelection(range.index + 3);
  }

  document.querySelectorAll('.quill-editor').forEach(function (editorEl) {
    var hiddenInput = editorEl.parentNode.querySelector('.quill-content');

    var quill = new Quill(editorEl, {
      theme: 'snow',
      modules: {
        toolbar: {
          container: [
            ['bold', 'italic', 'underline'],
            [{ header: [2, 3, false] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            ['link', 'image'],
            ['clean']
          ],
          handlers: {
            image: function () {
              var input = document.createElement('input');
              input.setAttribute('type', 'file');
              input.setAttribute('accept', 'image/*');
              input.click();

              var q = this.quill;
              input.onchange = function () {
                var file = input.files[0];
                if (!file) return;
                uploadImage(file).then(function (url) {
                  if (url) insertImage(q, url);
                });
              };
            }
          }
        },
        clipboard: { matchVisual: false }
      }
    });

    quill.root.addEventListener('paste', function (event) {
      var items = (event.clipboardData || window.clipboardData).items;
      if (!items) return;

      for (var i = 0; i < items.length; i++) {
        if (items[i].type.indexOf('image') === -1) continue;
        event.preventDefault();
        event.stopPropagation();

        var file = items[i].getAsFile();
        if (!file) return;

        uploadImage(file).then(function (url) {
          if (url) insertImage(quill, url);
        });
        return;
      }
    });

    quill.root.addEventListener('drop', function (event) {
      var files = event.dataTransfer && event.dataTransfer.files;
      if (!files || files.length === 0) return;

      var file = files[0];
      if (file.type.indexOf('image') === -1) return;
      event.preventDefault();
      event.stopPropagation();

      uploadImage(file).then(function (url) {
        if (url) insertImage(quill, url);
      });
    });

    if (hiddenInput && hiddenInput.value) {
      quill.root.innerHTML = hiddenInput.value;
    }

    var form = editorEl.closest('form');
    if (form) {
      form.addEventListener('submit', function () {
        if (hiddenInput) {
          hiddenInput.value = quill.root.innerHTML;
        }
      });
    }
  });
});
