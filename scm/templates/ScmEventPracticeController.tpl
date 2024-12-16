# IF C_HAS_GAMES #
    <article class="games modal-container">
        <div class="cell-flex cell-columns-3">
            # START items #
                <div id="{items.GAME_ID}" class="cell game-container" data-scroll="{items.GAME_DATE_TIMESTAMP}">
                    <div class="game-details small text-italic">
                        # IF C_PLAYGROUND #<span>{items.PLAYGROUND}</span># ENDIF #
                        <span>
                            # IF items.C_IS_LIVE #
                                <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                            # ELSE #
                                {items.GAME_DATE_HOUR_MINUTE}
                            # ENDIF #
                        </span>
                        <span>{items.GAME_ID}</span>
                    </div>
                    <div  class="id-{items.HOME_ID} game-team game-home# IF items.C_HOME_FAV # text-strong# ENDIF #"
                            # IF items.C_HOME_WIN # style="background-color: {items.WIN_COLOR}"# ENDIF #>
                        <div class="home-{items.GAME_ID} home-team">
                            # IF items.HOME_ID #
                                <div class="flex-team flex-left">
                                    # IF items.C_HAS_HOME_LOGO #<img src="{items.HOME_LOGO}" alt="{items.HOME_TEAM}"># ENDIF #
                                    <span>{items.HOME_TEAM}</span>
                                </div>
                            # ENDIF #
                        </div>
                        <div class="game-score home-score md-width-px-50">{items.HOME_SCORE}# IF items.C_HAS_PEN # <span class="small">({items.HOME_PEN})</span># ENDIF #</div>
                    </div>
                    <div class="id-{items.AWAY_ID} game-team game-away# IF items.C_AWAY_FAV # text-strong# ENDIF #"
                            # IF items.C_AWAY_WIN # style="background-color: {items.WIN_COLOR}"# ENDIF #>
                        <div class="away-{items.GAME_ID} away-team">
                            # IF items.AWAY_ID #
                                <div class="flex-team flex-left">
                                    # IF items.C_HAS_AWAY_LOGO #<img src="{items.AWAY_LOGO}" alt="{items.AWAY_TEAM}"># ENDIF #
                                    <span>{items.AWAY_TEAM}</span>
                                </div>
                            # ENDIF #
                        </div>
                        <div class="game-score away-score md-width-px-50">{items.AWAY_SCORE}# IF items.C_HAS_PEN # <span class="small">({items.AWAY_PEN})</span># ENDIF #</div>
                    </div>
                </div>
            # END items #
        </div>
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>