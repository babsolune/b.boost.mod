<section id="module-guide" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('guide', ID_CAT))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF NOT C_ROOT_CATEGORY #{@guide.module.title}# ENDIF #
			# IF C_DISPLAY_REORDER_LINK #
				<a class="offload" href="{U_REORDER_ITEMS}" aria-label="{@items.reorder}"><i class="fa fa-fw fa-exchange-alt" aria-hidden="true"></i></a>
			# ENDIF #
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1>
			{@guide.item.history}: 
			<span class="d-block small align-center">{ITEM_TITLE}</span>
		</h1>
	</header>

	<div class="sub-section">
		<div class="content-container">
			<div class="responsive-table">
				<table class="table">
					<thead>
						<tr>
							<th>{@common.version}</th>
							<th>{@common.creation.date}</th>
							<th>{@common.author}</th>
							<th>{@guide.change.reason}</th>
							# IF C_CONTROLS #
								<th>{@common.moderation}</th>
							# ENDIF #
						</tr>
					</thead>
					<tbody>
						# START items #
							<tr class="category-{items.CATEGORY_ID}# IF items.C_ACTIVE_CONTENT # active-content# ENDIF #">
								<td>
								# IF items.C_ACTIVE_CONTENT #
									<a href="{items.U_ITEM}" itemprop="name" class="offload">{@guide.archived.item}</a>
								# ELSE #
									<a href="{items.U_ARCHIVE}" itemprop="name" class="offload">{@guide.archived.item}</a>
								# ENDIF #
								</td>
								<td>
									<time datetime="{items.DATE_ISO8601}" itemprop="datePublished">{items.UPDATE_DATE_FULL}</time>
								</td>
								<td>
									{items.AUTHOR_DISPLAY_NAME}
								</td>
								<td# IF NOT items.C_INIT ## IF items.C_CHANGE_REASON # aria-label="{items.CHANGE_REASON}"# ENDIF ## ENDIF #>
									# IF items.C_INIT #
										{@guide.history.init}
									# ELSE #
										# IF items.C_CHANGE_REASON #<i class="fa fa-question" aria-hidden></i># ENDIF #
									# ENDIF #
								</td#>
								# IF C_CONTROLS #
									<td class="controls">
										# IF items.C_ACTIVE_CONTENT #
											<span class="small text-italic">{@guide.current.version}</span>
										# ELSE #
											# IF C_RESTORE #<a class="offload" href="{items.U_RESTORE}" aria-label="{@guide.restore.item}" data-confirmation="{@guide.restore.confirmation}"><i class="fa fa-fw fa-undo" aria-hidden="true"></i></a># ENDIF #
											# IF items.C_DELETE #
												<a href="{items.U_DELETE_CONTENT}" aria-label="{@guide.delete.version}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a>
											# ENDIF #
										# ENDIF #
									</td>
								# ENDIF #
							</tr>
						# END items #
					</tbody>
				</table>
			</div>			
		</div>
	</div>
	<footer>
		# IF C_PAGINATION #<div class="sub-section"><div class="content-container"># INCLUDE PAGINATION #</div></div># ENDIF #
	</footer>
</section>
<script src="{PATH_TO_ROOT}/guide/templates/js/guide# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
