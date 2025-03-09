<section id="module-scm" class="several-items">
	<header class="section-header flex-between flex-between-large">
		<h1 class="flex-between">
			{MODULE_NAME} 
		</h1>
        <div class="small message-helper bgc notice">{@scm.warning.current.season}</div>
	</header>

	# IF C_NO_ITEM #
		<div class="sub-section">
			<div class="content-container">
				<div class="content">
					<div class="message-helper bgc notice">{@scm.message.no.events}</div>
				</div>
			</div>
		</div>
	# ELSE #
        # IF C_CURRENT_GAMES_CONFIG #
            # INCLUDE CURRENT_GAMES #
        # ENDIF #

		<div class="sub-section">
			<div class="content-container">
				<article class="scm-item">
					<div class="content">
						<ul class="root-list # IF C_CONTROLS ## IF C_ROOT_SEVERAL_ITEMS # root-ul# ENDIF ## ENDIF #">
							# IF C_CONTROLS #
								# IF C_ROOT_SEVERAL_ITEMS #
									<a class="offload" class="reorder-items" href="{U_ROOT_REORDER_ITEMS}" aria-label="{@items.reorder}"><i class="fa fa-fw fa-exchange-alt"></i></a>
								# ENDIF #
							# ENDIF #
							# START root_items #
								<li class="flex-between">
									<a class="offload" class="categories-item d-block" href="{root_items.U_EVENT}"><i class="fa fa-fw fa-file-alt"></i> {root_items.TITLE}</a>
									# IF root_items.C_CONTROLS #
										<div class="controls">
											# IF root_items.C_EDIT #<a class="offload" href="{root_items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
											# IF root_items.C_DELETE #<a href="{root_items.U_DELETE}" data-confirmation="delete-element" aria-label="{@common.delete}" id="delete-{root_items.ID}"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
										</div>
									# ENDIF #
								</li>
							# END root_items #
						</ul>

						<nav id="category-nav" class="scm-list">
							<ul>
								# START categories #
									<li
											data-id="{categories.CATEGORY_ID}"
											data-p-id="{categories.CATEGORY_PARENT_ID}"
											data-order-id="{categories.CATEGORY_SUB_ORDER}">
										<div class="flex-between toggle-menu-button-{categories.CATEGORY_ID}">
											<div class="categories-item flex-between">
												<span><i class="far fa-fw fa-folder" aria-hidden="true"></i> {categories.CATEGORY_NAME}</span>
											</div>
											<a class="offload" href="{categories.U_CATEGORY}" aria-label="{@scm.category.history}">
                                                <i class="fa fa-fw fa-clock-rotate-left" aria-hidden="true"></i>
												<span class="small">({categories.ITEMS_NUMBER})</span>
                                            </a>
										</div>
										# IF categories.C_ITEMS #
											<ul class="items-list-{categories.CATEGORY_ID}">
												# IF C_CONTROLS #
													# IF categories.C_SEVERAL_ITEMS #
														<a class="offload" class="reorder-items" href="{categories.U_REORDER_ITEMS}" aria-label="{@items.reorder}"><i class="fa fa-fw fa-exchange-alt"></i></a>
													# ENDIF #
												# ENDIF #
												# START categories.items #
													<li
                                                            data-id="{categories.items.CATEGORY_ID}"
                                                            data-p-id="{categories.items.CATEGORY_PARENT_ID}"
                                                            data-order-id="{categories.items.CATEGORY_SUB_ORDER}"
                                                            class="flex-between">
														<a class="d-block categories-item offload" href="{categories.items.U_EVENT}"><i class="fa fa-fw fa-file-alt"></i> {categories.items.TITLE}</a>
														# IF categories.items.C_CONTROLS #
															<div class="controls">
																# IF categories.items.C_EDIT #<a class="offload" href="{categories.items.U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
																# IF categories.items.C_DELETE #<a href="{categories.items.U_DELETE}" data-confirmation="delete-element" aria-label="{@common.delete}" id="delete-{categories.items.ID}"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
															</div>
														# ENDIF #
													</li>
												# END categories.items #
											</ul>
										# ENDIF #
									</li>
								# END categories #
							</ul>
						</nav>
					</div>
				</article>

			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.events.current.js" defer></script>
