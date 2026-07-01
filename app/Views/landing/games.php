<style>
    #games {
        position: relative;
        overflow: hidden;
        isolation: isolate;
        background-color: #eef4fb;
        background-image:
            linear-gradient(rgba(17, 87, 137, 0.05) 1px, transparent 1px),
            linear-gradient(90deg, rgba(17, 87, 137, 0.05) 1px, transparent 1px);
        background-size: 24px 24px, 24px 24px;
        background-position: center, center;
    }

    #games::before {
        content: none;
    }

    #games::after {
        content: none;
    }

    #games > .max-w-[1200px] {
        position: relative;
        z-index: 1;
    }

    #games .games-kicker {
        color: #F8AF21;
    }

    #games .games-title {
        color: #0f3f62;
    }

    #games .games-subtitle {
        color: rgba(15, 63, 98, 0.78);
    }

    #games .games-catalog {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 460px));
        justify-content: center;
        gap: 1.15rem;
    }

    #games .game-card {
        border-radius: 16px;
        border: 1px solid rgba(15, 63, 98, 0.18);
        background: #ffffff;
        box-shadow: 0 26px 40px -30px rgba(12, 36, 58, 0.45);
        overflow: hidden;
        position: relative;
    }

    #games .game-card::before {
        content: "";
        position: absolute;
        inset: 0;
        pointer-events: none;
        border: 1px solid rgba(120, 168, 205, 0.16);
        border-radius: 16px;
    }

    #games .game-body {
        padding: 1.3rem;
        display: grid;
        gap: 0.82rem;
    }

    #games .game-hud {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.45rem;
    }

    #games .game-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 999px;
        padding: 0.24rem 0.56rem;
        font-size: 0.58rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        border: 1px solid rgba(15, 63, 98, 0.2);
        color: #24587d;
        background: #f3f8fc;
    }

    #games .game-title {
        margin: 0;
        color: #0f3f62;
        font-family: "DM Serif Display", serif;
        font-size: 2.36rem;
        line-height: 0.94;
    }

    #games .game-description {
        margin: 0;
        color: rgba(15, 63, 98, 0.8);
        font-size: 1rem;
        line-height: 1.48;
    }

    #games .game-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.6rem;
        color: rgba(15, 63, 98, 0.62);
        font-size: 0.74rem;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    #games .game-action {
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 0.82rem 1rem;
        border-radius: 10px;
        text-decoration: none;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    #games .game-action:hover {
        transform: none;
    }

    #games .game-action-play {
        background: #45a3d3;
        border: 1px solid #45a3d3;
        color: #f7fcff;
    }

    #games .game-action-play:hover {
        background: #3397cb;
    }

    #games .game-action-disabled {
        background: #edf3f8;
        border: 1px solid rgba(15, 63, 98, 0.16);
        color: rgba(15, 63, 98, 0.58);
        cursor: pointer;
    }

    @media (max-width: 640px) {
        #games .game-title {
            font-size: 2rem;
        }

        #games .game-description {
            font-size: 0.92rem;
        }
    }
</style>

<section id="games" class="relative py-16 md:py-24 px-6 md:px-10 lg:px-14">
    <div class="max-w-[1200px] mx-auto">
        <div class="text-center mb-8 md:mb-10">
            <div class="flex items-center justify-center gap-2 mb-3">
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
                <span class="games-kicker text-[.58rem] font-semibold tracking-[.2em] uppercase">Interactive</span>
                <span class="block w-[18px] h-[1.5px] bg-gold"></span>
            </div>
            <h2 class="games-title font-display text-3xl md:text-[2.2rem] leading-[1.12]">Games</h2>
        </div>

        <div class="games-catalog">
            <article class="game-card">
                <div class="game-body">
                    <div class="game-hud">
                        <p class="text-[.6rem] font-semibold tracking-[.16em] uppercase text-gold m-0">Featured Game</p>
                        <span class="game-chip">Single Player</span>
                    </div>
                    <h3 class="game-title">Guess The Startup</h3>
                    <p class="game-description">Fast 5-letter startup word challenge.</p>

                    <div class="game-meta">
                        <span>5 Letters</span>
                        <span>1 Play / Day</span>
                    </div>

                    <?php if (! empty($isGuessStartupEnabled)): ?>
                        <a href="<?= site_url('games/guess-the-startup') ?>" class="game-action game-action-play">Start Playing</a>
                    <?php else: ?>
                        <a href="<?= site_url('games/guess-the-startup') ?>" class="game-action game-action-disabled">View Lobby</a>
                    <?php endif; ?>
                </div>
            </article>
        </div>
    </div>
</section>
