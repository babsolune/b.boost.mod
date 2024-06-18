# IF C_ITEMS #
	<div class="cell-table">
		<table id="table-mini-football" class="table">
			<thead>
				<tr>
					<th aria-label="# IF C_SORT_BY_DATE #{@common.sort.by.date}# ELSE #{@football.ranking}# ENDIF #">
						<i class="fa# IF C_SORT_BY_DATE #r fa-calendar-alt# ELSE # fa-trophy# ENDIF # hidden-small-screens" aria-hidden="true"></i>
						<span class="hidden-large-screens"># IF C_SORT_BY_DATE #{@common.sort.by.date}# ELSE #{@football.ranking}# ENDIF #</span>
					</th>
					<th>{@common.name}</th>
					# IF NOT C_SORT_BY_DATE #
						<th aria-label="{@common.note}">
							<i class="far fa-star hidden-small-screens" aria-hidden="true"></i>
							<span class="hidden-large-screens">{@common.note}</span>
                        </th>
					# ENDIF #
				</tr>
			</thead>
			<tbody>
					# START items #
						<tr class="category-{items.CATEGORY_ID}">
							<td># IF C_SORT_BY_DATE #<time datetime="{items.DATE_ISO8601}">{items.DATE_DAY_MONTH}</time># ELSE #{items.DISPLAYED_POSITION}# ENDIF #</td>
							<td# IF C_SORT_BY_NOTATION # class="mini-football-table-name"# ENDIF #>
								<a class="offload" href="{items.U_COMPET}">
									{items.TITLE}
								</a>
								<p class="align-right small">
									<a class="offload" href="{items.U_CATEGORY}">
										<i class="far fa-folder" aria-hidden="true"></i> {items.CATEGORY_NAME}
									</a>
								</p>
							</td>
							# IF NOT C_SORT_BY_DATE #
								<td># IF C_SORT_BY_DOWNLOADS_NUMBER #{items.DOWNLOADS_NUMBER}# ELSE #{items.STATIC_NOTATION}# ENDIF #</td>
							# ENDIF #
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
