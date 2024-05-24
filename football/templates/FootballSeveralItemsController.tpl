<section id="module-football" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('football', CATEGORY_ID))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF NOT C_ROOT_CATEGORY #{@football.module.title}# ENDIF #
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1>
			# IF C_PENDING #
				{@football.pending.items}
			# ELSE #
				# IF C_MEMBER_ITEMS #
					# IF C_MY_ITEMS #{@football.my.items}# ELSE #{@football.member.items} {MEMBER_NAME}# ENDIF #
				# ELSE #
					# IF C_ROOT_CATEGORY #{@football.module.title}# ELSE ## IF C_TAG_ITEMS #<span class="smaller">{@common.keyword}: </span># ENDIF #{CATEGORY_NAME}# ENDIF #
				# ENDIF #
			# ENDIF #
		</h1>
	</header>

	# IF C_SUB_CATEGORIES #
		<div class="sub-section">
			<div class="content-container">
				<div class="cell-flex cell-tile cell-columns-{CATEGORIES_PER_ROW}">
					# START sub_categories_list #
						<div class="cell cell-category category-{sub_categories_list.CATEGORY_ID}">
							<div class="cell-header">
								<div class="cell-name"><a class="subcat-title offload" itemprop="about" href="{sub_categories_list.U_CATEGORY}">{sub_categories_list.CATEGORY_NAME}</a></div>
								<span class="small pinned notice" role="contentinfo" aria-label="{sub_categories_list.ITEMS_NUMBER} # IF sub_categories_list.C_SEVERAL_ITEMS #${TextHelper::lcfirst(@items)}# ELSE #${TextHelper::lcfirst(@item)}# ENDIF #">
									{sub_categories_list.ITEMS_NUMBER}
								</span>
							</div>
							# IF sub_categories_list.C_CATEGORY_THUMBNAIL #
								<div class="cell-body" itemprop="about">
									<div class="cell-thumbnail cell-landscape cell-center">
										<img itemprop="thumbnailUrl" src="{sub_categories_list.U_CATEGORY_THUMBNAIL}" alt="{sub_categories_list.CATEGORY_NAME}" />
										<a class="cell-thumbnail-caption offload" href="{sub_categories_list.U_CATEGORY}">
											{@category.see.category}
										</a>
									</div>
								</div>
							# ENDIF #
						</div>
					# END sub_categories_list #
				</div>
				# IF C_SUBCATEGORIES_PAGINATION #<div class="content align-center"># INCLUDE SUBCATEGORIES_PAGINATION #</div># ENDIF #
			</div>
		</div>
	# ENDIF #

	# IF C_ITEMS #
		<div class="sub-section">
			<div class="content-container">
				# IF C_SEVERAL_ITEMS #
					# IF NOT C_MEMBER_ITEMS #
						<div class="content">
							# INCLUDE SORT_FORM #
							<div class="spacer"></div>
						</div>
					# ENDIF #
				# ENDIF #
				# IF C_TABLE_VIEW #
					<div class="responsive-table">
						<table class="table">
							<thead>
								<tr>
									<th>{@common.title}</th>
									<th class="col-small" aria-label="{@common.creation.date}">
										<i class="far fa-fw fa-calendar-plus hidden-small-screens" aria-hidden="true"></i>
										<span class="hidden-large-screens">{@common.creation.date}</span>
									</th>
									<th class="col-small" aria-label="">
										<i class="fa fa-fw fa-football hidden-small-screens" aria-hidden="true"></i>
										<span class="hidden-large-screens"></span>
									</th>
									# IF C_ENABLED_VIEWS_NUMBER #
										<th class="col-small" aria-label="{@common.views.number}">
											<i class="fa fa-fw fa-eye hidden-small-screens" aria-hidden="true"></i>
											<span class="hidden-large-screens">{@common.views.number}</span>
										</th>
									# ENDIF #
									# IF C_ENABLED_NOTATION #
										<th aria-label="{@common.note}">
											<i class="far fa-fw fa-star hidden-small-screens" aria-hidden="true"></i>
											<span class="hidden-large-screens">{@common.note}</span>
										</th>
									# ENDIF #
									# IF C_ENABLED_COMMENTS #
										<th aria-label="{@common.comments}">
											<i class="far fa-fw fa-comments hidden-small-screens" aria-hidden="true"></i>
											<span class="hidden-large-screens">{@common.comments}</span>
										</th>
									# ENDIF #
									# IF C_CONTROLS #
										<th class="col-small" aria-label="{@common.moderation}">
											<i class="fa fa-fw fa-gavel hidden-small-screens" aria-hidden="true"></i>
											<span class="hidden-large-screens">{@common.moderation}</span>
										</th>
									# ENDIF #
								</tr>
							</thead>
							<tbody>
								# START items #
									<tr class="category-{items.CATEGORY_ID}">
										<td>
											<a href="{items.U_ITEM}" itemprop="name" class="offload# IF items.C_NEW_CONTENT # new-content# ENDIF #">{items.TITLE}</a>
										</td>
										<td>
											<time datetime="# IF NOT items.C_DIFFERED #{items.DATE_ISO8601}# ELSE #{items.DIFFERED_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished">
												# IF NOT items.C_DIFFERED #{items.DATE}# ELSE #{items.DIFFERED_START_DATE}# ENDIF #
											</time>
											# IF items.C_HAS_UPDATE_DATE #
												<time class="pinned notice small text-italic" aria-label="{@common.last.update}" datetime="{items.UPDATE_DATE_ISO8601}" itemprop="datePublished">{items.UPDATE_DATE}</time>
											# ENDIF #
										</td>
										<td>
											{items.DOWNLOADS_NUMBER}
										</td>
										# IF C_ENABLED_VIEWS_NUMBER #
											<td>
												{items.VIEWS_NUMBER}
											</td>
										# ENDIF #
										# IF C_ENABLED_NOTATION #
											<td>
												{items.STATIC_NOTATION}
											</td>
										# ENDIF #
										# IF C_ENABLED_COMMENTS #
											<td>
												{items.COMMENTS_NUMBER}
											</td>
										# ENDIF #
										# IF C_CONTROLS #
											<td class="controls">
												# IF items.C_EDIT #
													<a class="offload" href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a>
												# ENDIF #
												# IF items.C_DELETE #
													<a href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a>
												# ENDIF #
											</td>
										# ENDIF #
									</tr>
								# END items #
							</tbody>
						</table>
					</div>
				# ELSE #
					<div class="cell-flex cell-row cell-tile">
						# START items #
							<article id="football-item-{items.ID}" class="football-item category-{items.CATEGORY_ID} cell# IF items.C_NEW_CONTENT # new-content# ENDIF #" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
								<div class="cell-content flex-between">
									<a class="offload" href="{items.U_ITEM}" itemprop="name">{items.TITLE}</a>
									# IF items.C_CONTROLS #
										<div class="controls align-right">
											# IF items.C_EDIT #<a class="offload item-edit" href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
											# IF items.C_DELETE #<a class="item-delete" href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
										</div>
									# ENDIF #
								</div>
                                <meta itemprop="url" content="{items.U_ITEM}">
                                <meta itemprop="description" content="{items.U_ITEM}" />
							</article>
						# END items #
					</div>
				# ENDIF #
			</div>
		</div>
	# ELSE #
		# IF NOT C_HIDE_NO_ITEM_MESSAGE #
			<div class="sub-section">
				<div class="content-container">
					<div class="content">
						<div class="message-helper bgc notice align-center">
							{@common.no.item.now}
						</div>
					</div>
				</div>
			</div>
		# ENDIF #
	# ENDIF #
	<footer>
		# IF C_PAGINATION #<div class="sub-section"><div class="content-container"># INCLUDE PAGINATION #</div></div># ENDIF #
	</footer>
</section>
