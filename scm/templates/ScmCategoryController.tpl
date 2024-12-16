<section id="module-scm" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('scm', CATEGORY_ID))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF NOT C_ROOT_CATEGORY #{@scm.module.title}# ENDIF #
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1>
			# IF C_PENDING #
				{@scm.pending.items}
			# ELSE #
				# IF C_MEMBER_ITEMS #
					# IF C_MY_ITEMS #{@scm.my.items}# ELSE #{@scm.member.items} {MEMBER_NAME}# ENDIF #
				# ELSE #
					# IF C_ROOT_CATEGORY #{@scm.module.title}# ELSE ## IF C_TAG_ITEMS #<span class="smaller">{@common.keyword}: </span># ENDIF #{CATEGORY_NAME}# ENDIF #
				# ENDIF #
			# ENDIF #
		</h1>
	</header>

	# IF C_SUB_CATEGORIES #
		<div class="sub-section">
			<div class="content-container">
				<div class="cell-flex cell-tile cell-columns-4">
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
                <div class="cell-flex cell-columns-2 cell-tile">
                    # START items #
                        <article id="scm-item-{items.ID}" class="scm-item category-{items.CATEGORY_ID} cell# IF items.C_NEW_CONTENT # new-content# ENDIF #" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
                            <div class="cell-content">
                                <div class="flex-between">
                                    <a class="offload" href="{items.U_EVENT}" itemprop="name">{items.TITLE}</a>
                                    # IF items.C_CONTROLS #
                                        <div class="controls align-right">
                                            # IF items.C_EDIT #<a class="offload item-edit" href="{items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
                                            # IF items.C_DELETE #<a class="item-delete" href="{items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
                                        </div>
                                    # ENDIF #
                                </div>
                                # START items.sub_items #
                                    <div class="flex-between small">
                                        <a class="offload" href="{items.sub_items.U_EVENT}" itemprop="name">{items.sub_items.DIVISION_NAME}# IF items.sub_items.C_HAS_POOL # {items.sub_items.POOL}# ENDIF #</a>
                                        # IF items.sub_items.C_CONTROLS #
                                            <div class="controls align-right">
                                                # IF items.sub_items.C_EDIT #<a class="offload item-edit" href="{items.sub_items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
                                                # IF items.sub_items.C_DELETE #<a class="item-delete" href="{items.sub_items.U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
                                            </div>
                                        # ENDIF #
                                    </div>
                                # END items.sub_items #
                            </div>
                            <meta itemprop="url" content="{items.U_EVENT}">
                            <meta itemprop="description" content="{items.U_EVENT}" />
                        </article>
                    # END items #
                </div>
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
