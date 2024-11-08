# IF C_ITEMS #
	<div class="cell-table">
		<table id="table-mini-scm" class="table">
			<tbody>
                # START items #
                    <tr class="smaller text-italic">
                        <td colspan="2"><a href="{items.U_EVENT}">{items.GAME_DIVISION}</a></td>
                        <td>{items.GAME_CATEGORY}</td>
                    </tr>
                    <tr class="category-{items.CATEGORY_ID}">
                        <td><a href="{items.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{items.HOME_TEAM}</a></td>
                        <td>
                            <span class="small">
                                {items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}
                                <br>{items.GAME_DATE_HOUR}:{items.GAME_DATE_MINUTE}
                            </span>
                        </td>
                        <td><a href="{items.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{items.AWAY_TEAM}</a></td>
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
