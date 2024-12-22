# START categories #
    <div class="{categories.CSS_CLASS}">
        <h6 class="text-strong cell-content cell-100" style="margin: 0 var(--cell-gap) 0 0;">
            <a href="{categories.U_CATEGORY}" class="offload">{categories.CATEGORY_NAME}</a>
        </h6>
        # START categories.items #
            <div id="game-{categories.items.GAME_ID}" class="cell cell-game">
                <div class="small text-italic flex-between flex-between-large bgc-sub">
                    <div class="cell-pad">
                        # IF categories.items.C_IS_SUB #
                            <a class="offload small" href="{categories.items.U_MASTER_EVENT}">{categories.items.MASTER_EVENT}</a> - 
                            <a class="offload" href="{categories.items.U_EVENT}">{categories.items.GAME_DIVISION}</a>
                        # ELSE #
                            <a href="{categories.items.U_EVENT}" class="offload">{categories.items.EVENT_NAME}</a>
                        # ENDIF #
                    </div>
                    <div class="cell-pad">
                        # IF categories.items.C_IS_LIVE #
                            <span class="blink pinned bgc-full notice">{@scm.is.live}</span> -
                        # ELSE #
                            <time>{categories.items.GAME_DATE_FULL}</time> -
                        # ENDIF #
                        # IF categories.items.C_TYPE_GROUP # <a href="{categories.items.U_GROUP}" class="offload">{@scm.group} {categories.items.GROUP}</a># ENDIF #
                        # IF categories.items.C_TYPE_BRACKET # <a href="{categories.items.U_BRACKET}" class="offload">{categories.items.BRACKET}</a># ENDIF #
                        # IF categories.items.C_TYPE_DAY # <a href="{categories.items.U_DAY}" class="offload">{@scm.day} {categories.items.DAY}</a># ENDIF #
                    </div>
                </div>
                # IF categories.items.C_LATE #
                    <div class="bgc notice smaller text-italic align-center">{@scm.game.late}</div>
                # ENDIF #
                <div class="flex-between flex-between-large current-games# IF categories.items.C_EXEMPT # bgc notice# ENDIF #">
                    <div 
                            class="team-{categories.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50"
                            # IF categories.items.C_HOME_WIN # style="background-color: {categories.items.WIN_COLOR}"# ENDIF #>
                        <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF categories.items.C_HOME_FAV # text-strong# ENDIF #">
                            <span>{categories.items.HOME_TEAM}</span>
                            # IF categories.items.C_HAS_HOME_LOGO #<img src="{categories.items.HOME_LOGO}" alt="{categories.items.HOME_TEAM}"># ENDIF #
                        </div>
                        <div class="game-score home-score cell-pad sm-width-pc-20">{categories.items.HOME_SCORE}# IF categories.items.C_HAS_PEN # <span class="small">({categories.items.HOME_PEN})</span># ENDIF #</div>
                    </div>
                    <div 
                            class="team-{categories.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team"
                            # IF categories.items.C_AWAY_WIN # style="background-color: {categories.items.WIN_COLOR}"# ENDIF #>
                        <div class="game-score away-score cell-pad sm-width-pc-20">{categories.items.AWAY_SCORE}# IF categories.items.C_HAS_PEN # <span class="small">({categories.items.AWAY_PEN})</span># ENDIF #</div>
                        <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF categories.items.C_AWAY_FAV # text-strong# ENDIF #">
                            # IF categories.items.C_HAS_AWAY_LOGO #<img src="{categories.items.AWAY_LOGO}" alt="{categories.items.AWAY_TEAM}"># ENDIF #
                            <span>{categories.items.AWAY_TEAM}</span>
                        </div>
                    </div>
                </div>
            </div>
        # END categories.items #
    </div>
# END categories #