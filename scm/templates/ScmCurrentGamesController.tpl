<div class="sub-section">
    <div class="content-container">
        <article class="content">
            <header class="flex-between modal-container">
                <h2>{@scm.today.games}</h2>
                <a data-modal data-target="before-games" aria-label="{@scm.yesterday.games}"><i class="fa fa-fw fa-clock-rotate-left" aria-hidden="true"></i></a>
                <div id="before-games" class="modal modal-animation">
                    <div class="close-modal" aria-label="{@common.close}"></div>
                    <div class="content-panel">
                        <h4>{@scm.yesterday.games}</h4>
                        # INCLUDE BEFORE_GAMES_LIST #
                    </div>
                </div>
            </header>
            # IF C_CURRENT_GAMES #
                # INCLUDE GAMES_LIST #
            # ELSE #
                <div class="message-helper bgc notice">{@scm.no.current.games}</div>
            # ENDIF #
        </article>
    </div>
</div>
