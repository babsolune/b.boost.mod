# START categories #
    <div class="cell-format format-category">
        <h6 class="text-strong cell-content cell-100" style="margin: 0 var(--cell-gap) 0 0;">
            <a href="{categories.U_CATEGORY}" class="offload">{categories.CATEGORY_NAME}</a>
        </h6>
        # START categories.events #
            <h6>
                # IF categories.events.C_IS_SUB #<a href="{categories.events.U_MASTER_EVENT}" class="offload">{categories.events.MASTER_EVENT}</a> - # ENDIF #
                <a href="{categories.events.U_EVENT}" class="offload">{categories.events.EVENT}</a>
            </h6>
            <div class="# IF C_CLASS #cell-flex cell-columns-2# ENDIF #">
                # START categories.events.items #
                    <div id="game-{categories.events.items.GAME_ID}" class="cell cell-game">
                        <div class="small text-italic flex-between flex-between-large bgc-sub cell-pad">
                            # IF categories.events.items.C_IS_LIVE #
                                <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                            # ELSE #
                                <time>{categories.events.items.GAME_DATE_FULL}</time>
                            # ENDIF #
                            # IF categories.events.items.C_STATUS #
                                <div class="sm-width-pc-20 small text-italic align-center bgc notice">{categories.events.items.STATUS}</div>
                            # ENDIF #
                            # IF categories.events.items.C_TYPE_GROUP # <a href="{categories.events.items.U_GROUP}" class="offload"># IF categories.events.items.C_HAT_RANKING #{@scm.day} {categories.events.items.DAY}# ELSE #{@scm.group} {categories.events.items.GROUP}# ENDIF #</a># ENDIF #
                            # IF categories.events.items.C_TYPE_BRACKET # <a href="{categories.events.items.U_BRACKET}" class="offload">{categories.events.items.BRACKET}</a># ENDIF #
                            # IF categories.events.items.C_TYPE_DAY # <a href="{categories.events.items.U_DAY}" class="offload">{@scm.day} {categories.events.items.DAY}</a># ENDIF #
                        </div>
                        # IF categories.events.items.C_LATE #
                            <div class="bgc notice smaller text-italic align-center">{@scm.game.late}</div>
                        # ENDIF #
                        <div class="flex-between flex-between-large current-games# IF categories.events.items.C_EXEMPT # bgc notice# ENDIF #">
                            <div class="team-{categories.events.items.HOME_ID} flex-between sm-width-pc-100 md-width-pc-50">
                                <div class="game-team home-team cell-pad flex-team flex-right sm-width-pc-80# IF categories.events.items.C_HOME_FAV # text-strong# ENDIF #">
                                    <span>
                                        <a
                                            href="{categories.events.items.U_HOME_CALENDAR}"
                                            aria-label="{@scm.club.see.calendar}# IF categories.events.items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF categories.events.items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                            class="offload# IF categories.events.items.C_HOME_FAV # text-strong# ENDIF ## IF categories.events.items.HOME_FORFEIT # warning# ENDIF ## IF categories.events.items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                            {categories.events.items.HOME_TEAM}
                                        </a>
                                    </span>
                                    # IF categories.events.items.C_HAS_HOME_LOGO #<img src="{categories.events.items.HOME_LOGO}" alt="{categories.events.items.HOME_TEAM}"># ENDIF #
                                </div>
                                <div class="game-score home-score cell-pad sm-width-pc-20">{categories.events.items.HOME_SCORE}# IF categories.events.items.C_HAS_PEN # <span class="small">({categories.events.items.HOME_PEN})</span># ENDIF #</div>
                            </div>
                            <div class="hidden-small-screens">-</div>
                            <div class="team-{categories.events.items.AWAY_ID} flex-between sm-width-pc-100 md-width-pc-50 invert-team">
                                <div class="game-score away-score cell-pad sm-width-pc-20">{categories.events.items.AWAY_SCORE}# IF categories.events.items.C_HAS_PEN # <span class="small">({categories.events.items.AWAY_PEN})</span># ENDIF #</div>
                                <div class="game-team away-team cell-pad flex-team flex-left sm-width-pc-80# IF categories.events.items.C_AWAY_FAV # text-strong# ENDIF #">
                                    # IF categories.events.items.C_HAS_AWAY_LOGO #<img src="{categories.events.items.AWAY_LOGO}" alt="{categories.events.items.AWAY_TEAM}"># ENDIF #
                                    <span>
                                        <a
                                            href="{categories.events.items.U_AWAY_CALENDAR}"
                                            aria-label="{@scm.club.see.calendar}# IF categories.events.items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF categories.events.items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                            class="offload# IF categories.events.items.C_AWAY_FAV # text-strong# ENDIF ## IF categories.events.items.AWAY_FORFEIT # warning# ENDIF ## IF categories.events.items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                            {categories.events.items.AWAY_TEAM}
                                        </a>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                # END categories.events.items #
            </div>
        # END categories.events #
    </div>
# END categories #