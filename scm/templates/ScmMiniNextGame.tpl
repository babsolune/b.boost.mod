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
                        <td>{items.HOME_TEAM}</td>
                        <td>
                            <span class="small">
                                {items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}
                                <br>{items.GAME_DATE_HOUR}:{items.GAME_DATE_MINUTE}
                            </span>
                        </td>
                        <td>{items.AWAY_TEAM}</td>
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
