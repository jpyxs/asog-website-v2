(function(){
    var slider  = document.getElementById('progSlider');
    var track   = slider ? slider.parentElement : null;
    var prev    = document.getElementById('progPrev');
    var next    = document.getElementById('progNext');
    var pgLabel = document.getElementById('progPage');
    var cards   = document.querySelectorAll('.prog-card');
    if (!slider || !track || !prev || !next || !cards.length) return;

    var total = parseInt(slider.getAttribute('data-total'), 10) || cards.length;
    var page  = 0;

    function getPerPage(){
        if (window.innerWidth < 640)  return 1;
        if (window.innerWidth < 1024) return 2;
        return 4;
    }

    function totalPages(){
        return Math.ceil(total / getPerPage());
    }

    function layout(){
        var trackW = track.offsetWidth;
        var pp     = getPerPage();
        var cardW  = trackW / pp;

        slider.style.width = (cardW * total) + 'px';
        cards.forEach(function(c){ c.style.width = cardW + 'px'; });
    }

    function goToPage(p){
        var pp   = getPerPage();
        var maxP = totalPages() - 1;
        if (p < 0) p = 0;
        if (p > maxP) p = maxP;
        page = p;

        var trackW = track.offsetWidth;
        var cardW  = trackW / pp;
        var px     = -(page * pp * cardW);

        gsap.to(slider, { x: px, duration: .7, ease: 'power3.inOut' });

        pgLabel.textContent = (page + 1) + ' / ' + (maxP + 1);

        prev.style.opacity       = page <= 0    ? '.3' : '1';
        prev.style.pointerEvents = page <= 0    ? 'none' : 'auto';
        next.style.opacity       = page >= maxP ? '.3' : '1';
        next.style.pointerEvents = page >= maxP ? 'none' : 'auto';
    }

    [prev, next].forEach(function(btn){
        btn.addEventListener('mouseenter', function(){
            if (btn.style.pointerEvents !== 'none'){
                btn.style.borderColor = 'rgba(248,175,33,.45)';
                btn.style.color       = 'rgba(248,175,33,.95)';
            }
        });
        btn.addEventListener('mouseleave', function(){
            btn.style.borderColor = 'rgba(255,255,255,.15)';
            btn.style.color       = 'rgba(255,255,255,.40)';
        });
    });

    next.addEventListener('click', function(){ goToPage(page + 1); });
    prev.addEventListener('click', function(){ goToPage(page - 1); });

    layout();
    goToPage(0);

    var rt;
    window.addEventListener('resize', function(){
        clearTimeout(rt);
        rt = setTimeout(function(){ layout(); goToPage(page); }, 150);
    });
})();
