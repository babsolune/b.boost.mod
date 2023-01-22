<section id="module-guide" class="category-{CATEGORY_ID} single-item">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="{U_SYNDICATION}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			{@guide.module.title}# IF NOT C_ROOT_CATEGORY # - {CATEGORY_NAME}# ENDIF #
			# IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF #
		</div>
		<h1><span id="name" itemprop="name">{TITLE}</span></h1>		
	</header>
	<div class="sub-section">
		<div class="content-container">
			# IF NOT C_VISIBLE #
				<div class="content">
					# INCLUDE NOT_VISIBLE_MESSAGE #
				</div>
			# ENDIF #
			# IF C_ARCHIVE #
				<div class="content">
					# INCLUDE ARCHIVED_CONTENT #
				</div>
			# ENDIF #
			# INCLUDE LEVEL_MESSAGE #
			<article itemscope="itemscope" itemtype="https://schema.org/CreativeWork" id="guide-item-{ID}" class="guide-item# IF C_NEW_CONTENT # new-content# ENDIF #">
				<div class="flex-between">
					<div class="more">
						# IF C_AUTHOR_DISPLAYED #
							<span class="pinned" aria-label="{@common.author}">
								<i class="fa fa-user" aria-hidden="true"></i>
								# IF C_AUTHOR_CUSTOM_NAME #
									<span class="custom-author">{AUTHOR_CUSTOM_NAME}</span>
								# ELSE #
									# IF C_AUTHOR_EXISTS #
										<a itemprop="author" rel="author" class="{AUTHOR_LEVEL_CLASS} offload" href="{U_AUTHOR_PROFILE}" # IF C_AUTHOR_GROUP_COLOR # style="color:{AUTHOR_GROUP_COLOR}" # ENDIF #>{AUTHOR_DISPLAY_NAME}</a>
									# ELSE #
										{AUTHOR_DISPLAY_NAME}
									# ENDIF #
								# ENDIF #
							</span>
						# ENDIF #
						<div class="pinned">
							<i class="fa fa-calendar-alt" aria-hidden="true"></i>
							<time aria-label="{@common.creation.date}" datetime="# IF C_DIFFERED #{DIFFERED_START_DATE_ISO8601}# ELSE #{DATE_ISO8601}# ENDIF #" itemprop="datePublished"> # IF C_DIFFERED #{DIFFERED_START_DATE}# ELSE #{DATE}# ENDIF #</time>
						</div>
						<span class="pinned" aria-label="{@common.category}">
							<i class="fa fa-folder" aria-hidden="true"></i>
							<a class="offload" itemprop="about" href="{U_CATEGORY}">{CATEGORY_NAME}</a>
						</span>
						# IF C_ENABLED_VIEWS_NUMBER #<span class="pinned" aria-label="{@common.views.number}"><i class="fa fa-eye" aria-hidden="true"></i> {VIEWS_NUMBER}</span># ENDIF #
						# IF C_ENABLED_COMMENTS #<span class="pinned" aria-label="{@common.comments}"><i class="fa fa-comment" aria-hidden="true"></i> # IF C_COMMENTS # {COMMENTS_NUMBER} # ENDIF # {L_COMMENTS}</span># ENDIF #
						# IF C_VISIBLE #
							# IF C_ENABLED_NOTATION #
								<div class="pinned">{NOTATION}</div>
							# ENDIF #
						# ENDIF #
					</div>
						<div class="controls align-right">
							<a class="offload" href="{U_HISTORY}" aria-label="{@guide.item.history}"><i class="fa fa-fw fa-clock-rotate-left" aria-hidden="true"></i></a>
							# IF C_ARCHIVE #
								# IF C_CONTROLS #
									# IF C_RESTORE #<a class="offload" href="{U_RESTORE}" aria-label="{@guide.restore.item}"><i class="fa fa-fw fa-undo" aria-hidden="true"></i></a># ENDIF #
									# IF C_DELETE #<a href="{U_DELETE_CONTENT}" aria-label="{@guide.delete.version}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
								# ENDIF #
							# ELSE #
								# IF C_CONTROLS #
									# IF C_EDIT #<a class="offload" href="{U_EDIT}" aria-label="{@common.edit}"><i class="far fa-fw fa-edit" aria-hidden="true"></i></a># ENDIF #
									# IF C_DELETE #<a href="{U_DELETE}" aria-label="{@common.delete}" data-confirmation="delete-element"><i class="far fa-fw fa-trash-alt" aria-hidden="true"></i></a># ENDIF #
								# ENDIF #
							# ENDIF #
						</div>
				</div>
				# IF C_HAS_UPDATE_DATE #<span class="pinned notice small text-italic item-modified-date">{@common.last.update}: <time datetime="{UPDATE_DATE_ISO8601}" itemprop="dateModified">{UPDATE_DATE_FULL}</time></span># ENDIF #
				<div id="sheet-summary" class="cell-tile">
					<div class="cell-summary cell">
						<div class="cell-header">
							<h5 class="cell-name">{@guide.summary}</h5>
						</div>
						<div class="cell-list">
							<ul id="summary-list"></ul>
						</div>
					</div>
				</div>

				<div class="content">
					# IF C_HAS_THUMBNAIL #
						<div class="item-thumbnail">
							<img src="{U_THUMBNAIL}" alt="{NAME}" itemprop="image" />
						</div>
					# ENDIF #
					<div itemprop="text">{CONTENT}</div>
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
<script src="{PATH_TO_ROOT}/guide/templates/js/guide# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
