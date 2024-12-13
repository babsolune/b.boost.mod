# IF C_ITEMS #
    <div class="cell-table">
        <table id="table-mini-scm" class="">
            <tbody>
                # START items #
                    <tr>
                        <td class="text-italic bgc-sub">
                            <span class="text-strong">{items.GAME_CATEGORY}</span>
                            # IF items.C_IS_SUB #<a class="offload d-block smaller" href="{items.U_MASTER_EVENT}">{items.MASTER_EVENT}</a># ENDIF #
                        </td>
                        <td colspan="2" class="bgc-sub smaller">
                            <span>{@scm.day.short}{items.CLUSTER} : {items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}/{items.YEAR} {items.GAME_DATE_HOUR}:{items.GAME_DATE_MINUTE}</span>
                            <a class="offload d-block" href="{items.U_EVENT}">{items.GAME_DIVISION}</a>
                        </td>
                    </tr>
                    # IF items.C_LATE #<tr><td class="bgc notice smaller text-italic" colspan="3">{@scm.game.late}</td></tr># ENDIF #
                    <tr class="category-{items.CATEGORY_ID}# IF items.C_EXEMPT # bgc notice# ENDIF #">
                        <td class="align-right# IF items.C_HOME_EXEMPT # small text-italic# ENDIF #">
                            <a
                                href="{items.U_HOME_CALENDAR}"
                                aria-label="{@scm.see.club.calendar}# IF items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                class="offload# IF items.C_HOME_FAV # text-strong# ENDIF ## IF items.HOME_FORFEIT # warning# ENDIF ## IF items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                {items.HOME_TEAM}
                            </a>
                        </td>
                        # IF items.C_STATUS #
                            <td class="md-width-px-70 small text-italic warning">{items.STATUS}</td>
                        # ELSE #
                            <td class="md-width-px-70"># IF items.C_HAS_SCORE #{items.HOME_SCORE} - {items.AWAY_SCORE}# ENDIF #</td>
                        # ENDIF #
                        <td class="align-left# IF items.C_AWAY_EXEMPT # small text-italic# ENDIF #">
                            <a
                                href="{items.U_AWAY_CALENDAR}"
                                aria-label="{@scm.see.club.calendar}# IF items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                class="offload# IF items.C_AWAY_FAV # text-strong# ENDIF ## IF items.AWAY_FORFEIT # warning# ENDIF ## IF items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                {items.AWAY_TEAM}
                            </a>
                        </td>
                    </tr>
                # END items #
            </tbody>
        </table>
    </div>
# ELSE #
	<div class="cell-alert">
		<div class="message-helper bgc notice">{@scm.message.no.games}</div>
	</div>
# ENDIF #

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>