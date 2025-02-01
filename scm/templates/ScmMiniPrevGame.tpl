# IF C_ITEMS #
    # START items #
        <div class="cell no-style">
            <div class="flex-between bgc-sub cell-pad">
                <div class="text-italic md-width-pc-50 align-center">
                    <span class="text-strong">{items.GAME_CATEGORY}</span>
                    # IF items.C_IS_SUB #<a class="offload d-block smaller" href="{items.U_MASTER_EVENT}">{items.MASTER_EVENT}</a># ENDIF #
                </div>
                <div class="smaller md-width-pc-50 align-center">
                    <span>{@scm.day.short}{items.CLUSTER} : {items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}/{items.YEAR} {items.GAME_DATE_HOUR}:{items.GAME_DATE_MINUTE}</span>
                    <a class="offload d-block" href="{items.U_EVENT}">{items.GAME_DIVISION}</a>
                </div>
            </div>
            # IF items.C_LATE #<div class="bgc notice smaller text-italic align-center" colspan="3">{@scm.game.late}</div># ENDIF #
            <div class="flex-between category-{items.CATEGORY_ID} cell-pad# IF items.C_EXEMPT # bgc notice# ENDIF #">
                <div class="md-width-pc-40 align-right# IF items.C_HOME_EXEMPT # small text-italic# ENDIF #">
                    <a
                        href="{items.U_HOME_CALENDAR}"
                        aria-label="{@scm.club.see.calendar}# IF items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                        class="offload# IF items.C_HOME_FAV # text-strong# ENDIF ## IF items.HOME_FORFEIT # warning# ENDIF ## IF items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                        {items.HOME_TEAM}
                    </a>
                </div>
                # IF items.C_STATUS #
                    <div class="md-width-pc-20 small text-italic warning align-center">{items.STATUS}</d>
                # ELSE #
                    <div class="md-width-pc-20 align-center"># IF items.C_HAS_SCORE #{items.HOME_SCORE} - {items.AWAY_SCORE}# ENDIF #</div>
                # ENDIF #
                <div class="md-width-pc-40 align-left# IF items.C_AWAY_EXEMPT # small text-italic# ENDIF #">
                    <a
                        href="{items.U_AWAY_CALENDAR}"
                        aria-label="{@scm.club.see.calendar}# IF items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                        class="offload# IF items.C_AWAY_FAV # text-strong# ENDIF ## IF items.AWAY_FORFEIT # warning# ENDIF ## IF items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                        {items.AWAY_TEAM}
                    </a>
                </div>
            </div>
        </div>
    # END items #
# ELSE #
	<div class="cell-alert">
		<div class="message-helper bgc notice">{@scm.message.no.games}</div>
	</div>
# ENDIF #

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>