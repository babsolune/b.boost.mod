# IF C_ITEMS #
    <div class="cell-table">
        <table id="table-mini-scm" class="">
            <tbody>
                # START items #
                    <tr>
                        <td class="smaller text-italic">
                            <span class="d-block text-strong">{items.GAME_CATEGORY}</span>
                            <a href="{items.U_EVENT}">{items.GAME_DIVISION}</a>
                        </td>
                        <td colspan="2">
                            <span class="small">{items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}/{items.YEAR}</span>
                            {items.GAME_DATE_HOUR}:{items.GAME_DATE_MINUTE}
                        </td>
                    </tr>
                    <tr class="category-{items.CATEGORY_ID}">
                        <td class="align-right"><a href="{items.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload# IF items.C_HOME_FAV # text-strong# ENDIF ## IF items.HOME_FORFEIT # warning# ENDIF #">{items.HOME_TEAM}</a></td>
                        <td class="width-px-70">
                            <span>{items.HOME_SCORE} - {items.AWAY_SCORE}</span>
                        </td>
                        <td class="align-left"><a href="{items.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload# IF items.C_AWAY_FAV # text-strong# ENDIF ## IF items.AWAY_FORFEIT # warning# ENDIF #">{items.AWAY_TEAM}</a></td>
                    </tr>
                # END items #
            </tbody>
        </table>
    </div>
# ELSE #
	<div class="cell-alert">
		<div class="message-helper bgc notice">{@common.no.item.now}</div>
	</div>
# ENDIF #

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>