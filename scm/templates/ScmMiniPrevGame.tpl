# IF C_ITEMS #
    <div class="cell-table">
        <table id="table-mini-scm" class="">
            <tbody>
                # START items #
                    <tr class="smaller text-italic">
                        <td colspan="2"><a href="{items.U_EVENT}">{items.GAME_DIVISION}</a></td>
                        <td>{items.GAME_CATEGORY}</td>
                    </tr>
                    <tr class="category-{items.CATEGORY_ID}">
                        <td class="align-right">{items.HOME_TEAM}</td>
                        <td class="width-px-70">
                            <span class="d-block">{items.HOME_SCORE} - {items.AWAY_SCORE}</span>
                            <span class="small">{items.GAME_DATE_DAY}/{items.GAME_DATE_MONTH}</span>
                        </td>
                        <td class="align-left">{items.AWAY_TEAM}</td>
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