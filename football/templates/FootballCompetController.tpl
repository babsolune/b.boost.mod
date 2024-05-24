<section id="module-football" class="category-{CATEGORY_ID} single-item">
	# INCLUDE MENU #
	<div class="sub-section">
		<div class="content-container">
			# IF NOT C_VISIBLE #
				<div class="content">
					# INCLUDE NOT_VISIBLE_MESSAGE #
				</div>
			# ENDIF #
			<article itemscope="itemscope" itemtype="https://schema.org/CreativeWork" id="football-item-{ID}" class="football-item# IF C_NEW_CONTENT # new-content# ENDIF #">
				# IF C_CONTROLS #
					<div class="controls align-right">
                        # IF C_EDIT #<a class="offload" href="{U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
						# IF C_DELETE #<a href="{U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
					</div>
				# ENDIF #

				<div class="content">
					<div itemprop="text"># INCLUDE RANKING #</div>
					<div itemprop="text"># INCLUDE TOURNEY_CALENDAR #</div>
				</div>

				<aside>${ContentSharingActionsMenuService::display()}</aside>

				# IF C_SOURCES #
					<aside class="sources-container">
						<span class="text-strong"><i class="fa fa-map-signs" aria-hidden="true"></i> {@common.sources}</span> :
						# START sources #
							<a itemprop="isBasedOnUrl" href="{sources.URL}" class="pinned link-color offload" rel="nofollow">{sources.NAME}</a># IF sources.C_SEPARATOR ## ENDIF #
						# END sources #
					</aside>
				# ENDIF #
				# IF C_KEYWORDS #
					<aside class="tags-container">
						<span class="text-strong"><i class="fa fa-tags" aria-hidden="true"></i> {@common.keywords} : </span>
						# START keywords #
							<a class="pinned link-color offload" href="{keywords.URL}">{keywords.NAME}</a># IF keywords.C_SEPARATOR #, # ENDIF #
						# END keywords #
					</aside>
				# ENDIF #
				# IF C_ENABLED_COMMENTS #
					<aside>
						# INCLUDE COMMENTS #
					</aside>
				# ENDIF #
			</article>
		</div>
	</div>
	<footer>
		<meta itemprop="url" content="{U_ITEM}">
		<meta itemprop="description" content="${escape(SUMMARY)}" />
		# IF C_ENABLED_COMMENTS #
			<meta itemprop="discussionUrl" content="{U_COMMENTS}">
			<meta itemprop="interactionCount" content="{COMMENTS_NUMBER} UserComments">
		# ENDIF #
	</footer>
</section>
