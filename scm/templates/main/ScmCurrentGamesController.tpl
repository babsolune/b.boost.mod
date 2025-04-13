<div class="sub-section">
    <div class="content-container">
        <article class="content">
            <header class="flex-between modal-container">
                <h2>{@scm.today.games}</h2>
                <div class="controls">
                    <span class="modal-button --before-games" aria-label="{@scm.yesterday.games}"><i class="fa fa-fw fa-clock-rotate-left" aria-hidden="true"></i></span>
                    <div id="before-games" class="modal">
                        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                        <div class="modal-content">
                            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                            <h4>{@scm.yesterday.games}</h4>
                            # INCLUDE BEFORE_GAMES_LIST #
                        </div>
                    </div>
                    <span class="modal-button --before-games" aria-label="{@scm.week.games}"><i class="fa fa-fw fa-calendar-alt" aria-hidden="true"></i></span>
                    <div id="before-games" class="modal">
                        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                        <div class="modal-content">
                            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                            <h4>{@scm.week.games}</h4>
                            # INCLUDE WEEK_GAMES_LIST #
                        </div>
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
