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
            # IF items.C_LATE #<div class="bgc notice smaller text-italic align-center">{@scm.game.late}</div># ENDIF #
            <div class="flex-between cell-pad">
                <div class="align-right md-width-pc-45"><a href="{items.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF items.C_HOME_FAV # text-strong# ENDIF ## IF items.HOME_FORFEIT # warning# ENDIF #">{items.HOME_TEAM}</a></div>
                <div class="align-center">&nbsp;|&nbsp;</div>
                <div class="align-left md-width-pc-45"><a href="{items.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF items.C_AWAY_FAV # text-strong# ENDIF ## IF items.AWAY_FORFEIT # warning# ENDIF #">{items.AWAY_TEAM}</a></div>
            </div>
        </div>
    # END items #
# ELSE #
	<div class="cell-alert">
		<div class="message-helper bgc notice">{@scm.message.no.games}</div>
	</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
