(function () {
  var shareBox = document.getElementById('storyShareBox');
  if (!shareBox) return;

  var copyBtn = document.getElementById('copyStoryLink');
  var nativeBtn = document.getElementById('nativeStoryShare');
  var shareUrl = shareBox.getAttribute('data-share-url') || window.location.href;
  var shareTitle = shareBox.getAttribute('data-share-title') || document.title;

  function setCopyButtonState(copied) {
    if (!copyBtn) return;
    var icon = copyBtn.querySelector('i');
    if (!icon) return;

    if (copied) {
      icon.className = 'fa-solid fa-check';
      copyBtn.setAttribute('title', 'Copied');
      copyBtn.setAttribute('aria-label', 'Copied');
    } else {
      icon.className = 'fa-solid fa-link';
      copyBtn.setAttribute('title', 'Copy story link');
      copyBtn.setAttribute('aria-label', 'Copy story link');
    }
  }

  if (nativeBtn && navigator.share) {
    nativeBtn.classList.remove('hidden');
    nativeBtn.addEventListener('click', async function () {
      try {
        await navigator.share({ title: shareTitle, url: shareUrl });
      } catch (err) {
        // Ignore cancellation errors from native share sheets.
      }
    });
  }

  if (!copyBtn) return;

  copyBtn.addEventListener('click', async function () {
    try {
      await navigator.clipboard.writeText(shareUrl);
      setCopyButtonState(true);
      setTimeout(function () {
        setCopyButtonState(false);
      }, 1400);
    } catch (err) {
      var temp = document.createElement('textarea');
      temp.value = shareUrl;
      temp.setAttribute('readonly', '');
      temp.style.position = 'absolute';
      temp.style.left = '-9999px';
      document.body.appendChild(temp);
      temp.select();
      document.execCommand('copy');
      document.body.removeChild(temp);
      setCopyButtonState(true);
      setTimeout(function () {
        setCopyButtonState(false);
      }, 1400);
    }
  });
})();
