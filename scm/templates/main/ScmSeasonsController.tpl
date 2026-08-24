<section id="module-scm" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('scm', CATEGORY_ID))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF NOT C_ROOT_CATEGORY #{@scm.module.title}# ENDIF #
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1>
			{@scm.division.list}
		</h1>
	</header>

	# IF C_SEASONS #
		<div class="sub-section">
			<div class="content-container">
                # START seasons #
                    <span id="season-{seasons.SEASON_ID}" class="big button modal-button --panel-{seasons.SEASON_ID}">{seasons.SEASON_NAME}</span>
                    <div id="panel-{seasons.SEASON_ID}" class="modal modal-half">
                        <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                        <div class="modal-content">
                            <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                            <ul>
                                # START seasons.events #
                                <li><a class="offload" href="{seasons.events.U_EVENT}">{seasons.events.EVENT_NAME}</a></li>
                                # END seasons.events #
                            </ul>
                        </div>
                    </div>
                # END seasons #
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
	</footer>
</section>
