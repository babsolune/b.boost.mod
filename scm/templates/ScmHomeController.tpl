<section id="module-scm" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('scm', CATEGORY_ID))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1 class="flex-between">
			{MODULE_NAME}
		</h1>
	</header>

	# IF C_CATEGORY_DESCRIPTION #
		<div class="sub-section">
			<div class="content-container">
				<div class="cat-description">
					{CATEGORY_DESCRIPTION}
				</div>
			</div>
		</div>
	# ENDIF #

	# IF C_NO_ITEM #
		<div class="sub-section">
			<div class="content-container">
				<div class="content">
					<div class="message-helper bgc notice">{@common.no.item.now}</div>
				</div>
			</div>
		</div>
	# ELSE #
        # IF C_CURRENT_GAMES #
            <div class="sub-section">
                <div class="content-container">
                    <article class="content">
                        <header><h2>{@scm.current.games}</h2></header>
                        <div class="cell-flex cell-columns-4">
                            # START current_games #
                                <div id="{current_games.GAME_ID}" class="cell game-container">
                                    <div class="game-details small text-italic">
                                        <a href="{current_games.U_EVENT}" class="offload">
                                        {current_games.EVENT_NAME}
                                        </a>
                                    </div>
                                    <div  class="id-{current_games.HOME_ID} game-team game-home# IF current_games.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF current_games.C_HOME_WIN # style="background-color: {current_games.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{current_games.GAME_ID} home-team">
                                            # IF current_games.HOME_ID #
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{current_games.HOME_LOGO}" alt="{current_games.HOME_TEAM}">
                                                    <span>{current_games.HOME_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score home-score width-px-50">{current_games.HOME_SCORE}# IF current_games.C_HAS_PEN # <span class="small">({current_games.HOME_PEN})</span># ENDIF #</div>
                                    </div>
                                    <div class="id-{current_games.AWAY_ID} game-team game-away# IF current_games.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF current_games.C_AWAY_WIN # style="background-color: {current_games.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{current_games.GAME_ID} away-team">
                                            # IF current_games.AWAY_ID #
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{current_games.AWAY_LOGO}" alt="{current_games.AWAY_TEAM}">
                                                    <span>{current_games.AWAY_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score away-score width-px-50">{current_games.AWAY_SCORE}# IF current_games.C_HAS_PEN # <span class="small">({current_games.AWAY_PEN})</span># ENDIF #</div>
                                    </div>
                                </div>
                            # END current_games #
                        </div>
                    </article>
                </div>
            </div>
        # ENDIF #
        # IF C_NEXT_GAMES #
            <div class="sub-section">
                <div class="content-container">
                    <article class="content">
                        <header><h2>{@scm.next.games}</h2></header>
                        <div class="cell-flex cell-columns-4">
                            # START next_games #
                                <div id="{next_games.GAME_ID}" class="cell game-container">
                                    <div class="game-details small text-italic">
                                        <a href="{next_games.U_EVENT}" class="offload">
                                            {next_games.EVENT_NAME}
                                        </a>
                                        <time>{next_games.GAME_DATE_FULL}</time>
                                    </div>
                                    <div  class="id-{next_games.HOME_ID} game-team game-home# IF next_games.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF next_games.C_HOME_WIN # style="background-color: {next_games.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{next_games.GAME_ID} home-team">
                                            # IF next_games.HOME_ID #
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{next_games.HOME_LOGO}" alt="{next_games.HOME_TEAM}">
                                                    <span>{next_games.HOME_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score home-score width-px-50">{next_games.HOME_SCORE}# IF next_games.C_HAS_PEN # <span class="small">({next_games.HOME_PEN})</span># ENDIF #</div>
                                    </div>
                                    <div class="id-{next_games.AWAY_ID} game-team game-away# IF next_games.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF next_games.C_AWAY_WIN # style="background-color: {next_games.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{next_games.GAME_ID} away-team">
                                            # IF next_games.AWAY_ID #
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{next_games.AWAY_LOGO}" alt="{next_games.AWAY_TEAM}">
                                                    <span>{next_games.AWAY_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score away-score width-px-50">{next_games.AWAY_SCORE}# IF next_games.C_HAS_PEN # <span class="small">({next_games.AWAY_PEN})</span># ENDIF #</div>
                                    </div>
                                </div>
                            # END next_games #
                        </div>
                    </article>
                </div>
            </div>
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
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js" defer></script>
