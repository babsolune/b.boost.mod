<section id="module-modcatfull">
	<header>
		<h1>
			<a href="{U_SYNDICATION}" title="${LangLoader::get_message('syndication', 'common')}"><i class="fa fa-syndication"></i></a>
			{@modcatfull.module.title}# IF NOT C_ROOT_CATEGORY # - {CATEGORY_NAME}# ENDIF # # IF IS_ADMIN #<a href="{U_EDIT_CATEGORY}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit smaller"></i></a># ENDIF #
		</h1>
	</header>
	# INCLUDE NOT_VISIBLE_MESSAGE #
	<article itemscope="itemscope" itemtype="http://schema.org/Itemcatfull" id="article-modcatfull-{ID}" class="article-modcatfull# IF C_NEW_CONTENT # new-content# ENDIF #">
		<header>
			<h2>
				<span itemprop="name">{TITLE}</span>
				<span class="actions">
					# IF C_EDIT #
						<a href="{U_EDIT_ITEM}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit"></i></a>
					# ENDIF #
					# IF C_DELETE #
						<a href="{U_DELETE_ITEM}" title="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="fa fa-delete"></i></a>
					# ENDIF #
						<a href="{U_PRINT_ITEM}" title="${LangLoader::get_message('printable_version', 'main')}" target="blank"><i class="fa fa-print"></i></a>
				</span>
			</h2>

			<div class="more">
				# IF C_DISPLAYED_AUTHOR #
				<i class="fa fa-user" title="${LangLoader::get_message('author', 'common')}"></i>
					# IF C_CUSTOM_AUTHOR_NAME #
						{CUSTOM_AUTHOR_NAME}
					# ELSE #
						# IF C_AUTHOR_EXIST #<a itemprop="author" href="{U_AUTHOR}" class="{USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{USER_GROUP_COLOR}"# ENDIF #>&nbsp;{PSEUDO}&nbsp;</a># ELSE #{PSEUDO}# ENDIF #|&nbsp;
					# ENDIF #
				# ENDIF #
				<i class="fa fa-calendar" title="${LangLoader::get_message('date', 'date-common')}"></i>&nbsp;<time datetime="# IF NOT C_DIFFERED #{DATE_ISO8601}# ELSE #{PUBLICATION_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished"># IF NOT C_DIFFERED #{DATE}# ELSE #{PUBLICATION_START_DATE}# ENDIF #</time>&nbsp;|
				&nbsp;<i class="fa fa-eye" title="{VIEWS_NUMBER} {@modcatfull.sort.field.views}"></i>&nbsp;<span title="{VIEWS_NUMBER} {@modcatfull.sort.field.views}">{VIEWS_NUMBER}</span>
				# IF C_COMMENTS_ENABLED #
					&nbsp;|&nbsp;<i class="fa fa-comment" title="${LangLoader::get_message('comments', 'comments-common')}"></i><a itemprop="discussionUrl" class="small" href="{U_COMMENTS}">&nbsp;{L_COMMENTS}</a>
				# ENDIF #
				# IF C_KEYWORDS #
				&nbsp;|&nbsp;<i title="${LangLoader::get_message('form.keywords', 'common')}" class="fa fa-tags"></i>
					# START keywords #
						<a itemprop="keywords" href="{keywords.URL}">{keywords.NAME}</a># IF keywords.C_SEPARATOR #, # ENDIF #
					# END keywords #
				# ENDIF #
			</div>

			<meta itemprop="url" content="{U_ITEM}">
			<meta itemprop="description" content="${escape(DESCRIPTION)}">
			<meta itemprop="datePublished" content="# IF NOT C_DIFFERED #{DATE_ISO8601}# ELSE #{PUBLICATION_START_DATE_ISO8601}# ENDIF #">
			<meta itemprop="discussionUrl" content="{U_COMMENTS}">
			# IF C_HAS_THUMBNAIL #<meta itemprop="thumbnailUrl" content="{THUMBNAIL}"># ENDIF #
			<meta itemprop="interactionCount" content="{COMMENTS_NUMBER} UserComments">
		</header>
		<div class="content">
			# IF C_PAGINATION #
				# INCLUDE FORM #
				<div class="spacer"></div>
			# ENDIF #
			# IF PAGE_NAME #
				<h2 class="item-title-page">{PAGE_NAME}</h2>
			# ENDIF #

			# IF C_FIRST_PAGE #
				# IF C_CAROUSEL #
				 	# START carousel #
						<a href="# IF carousel.C_PTR #{PATH_TO_ROOT}# ENDIF #{carousel.URL}" title="{carousel.NAME}" data-lightbox="formatter" data-rel="lightcase:collection">
							<figure class="carousel-thumbnail">
								<img src="# IF carousel.C_PTR #{PATH_TO_ROOT}# ENDIF #{carousel.URL}" alt="{carousel.NAME}" />
								<figcaption>{carousel.NAME}</figcaption>
							</figure>
						</a>
				 	# END carousel #
				# ELSE #
					# IF C_HAS_THUMBNAIL #<img src="{THUMBNAIL}" alt="{TITLE}" class="thumbnail-item" /># ENDIF #
				# ENDIF #
			# ENDIF #

			<div itemprop="text">{CONTENTS}</div>
		</div>
		<aside>
			# IF C_PAGINATION #
				<hr />
				<div class="pages-pagination right">
					# IF C_NEXT_PAGE #
					<a href="{U_NEXT_PAGE}">{L_NEXT_TITLE} <i class="fa fa-arrow-right"></i></a>
					# ELSE #
					&nbsp;
					# ENDIF #
				</div>
				<div class="pages-pagination center"># INCLUDE ITEMS_PAGINATION #</div>
				<div class="pages-pagination">
					# IF C_PREVIOUS_PAGE #
					<a href="{U_PREVIOUS_PAGE}"><i class="fa fa-arrow-left"></i> {L_PREVIOUS_TITLE}</a>
					# ENDIF #
				</div>
				<div class="spacer"></div>
			# ENDIF #

			# IF C_WEBLINK #
				<hr />
				<span class="actions">
					<i class="fa fa-sign-out" title="{@modcatfull.visits.number}"></i> {VISITS_NUMBER}
				</span>
				<a href="{U_WEBLINK}" class="button submit">{@modcatfull.visit}</a>
				<div class="spacer"></div>
			# ENDIF #

			# IF C_VISIBLE_LINKS #
				# IF C_FILE #
					<hr />
					<span class="actions">
						<i class="fa fa-microchip" title="{@modcatfull.visits.number}"></i> {FILE_SIZE} |
						<i class="fa fa-download" title="{@modcatfull.downloads.number}"></i> {DOWNLOADS_NUMBER}
					</span>
					<a href="{U_FILE}" class="button submit">{@modcatfull.file}</a>
					<div class="spacer"></div>
				# ENDIF #
			# ENDIF #

			# IF IS_USER_CONNECTED #
				# IF C_DEADLINK #
					<hr />
					<span class="actions">
						<i class="fa fa-unlink"></i>
					</span>
					<a href="{U_DEADLINK}" class="button alt">{@modcatfull.dead.link}</a>
					<div class="spacer"></div>
				# ENDIF #
			# ENDIF #

			# IF C_SOURCES #
				<hr />
				<div id="modcatfull-sources-container">
					<span>${LangLoader::get_message('form.sources', 'common')}</span> :
					# START sources #
					<a itemprop="isBasedOnUrl" href="{sources.URL}" class="small">{sources.NAME}</a># IF sources.C_SEPARATOR #, # ENDIF #
					# END sources #
				</div>
			# ENDIF #

			# IF C_UPDATED_DATE #
				<hr />
				<div>
					<i>${LangLoader::get_message('form.date.update', 'common')} : <time datetime="{UPDATED_DATE_ISO8601}" itemprop="datePublished">{UPDATED_DATE}</time></i>
				</div>
			# ENDIF #

			# IF C_SUGGESTED_ITEMS #
				<hr />
				<h6><i class="fa fa-lightbulb-o"></i> ${LangLoader::get_message('suggestions', 'common')} :</h6>
				<div class="elements-container columns-{SUGGESTED_COLUMNS} no-style">
					# START suggested_items #
					<div class="block suggested-thumbnail">
						<a href="{suggested_items.U_ITEM}">
							<figure>
								# IF suggested_items.C_HAS_THUMBNAIL #<img src="# IF suggested_items.C_PTR #{PATH_TO_ROOT}# ENDIF #{suggested_items.THUMBNAIL}" alt="{suggested_items.TITLE}" /># ENDIF #
								<figcaption>{suggested_items.TITLE}</figcaption>
							</figure>
						</a>
					</div>
					# END suggested_items #
				</div>
			# ENDIF #

			# IF C_NAVIGATION_LINKS #
				<hr />
				<div class="navigation-link">
					# IF C_PREVIOUS_ITEM #
						<span class="navigation-link-previous">
							<a href="{U_PREVIOUS_ITEM}">
								<figure class="navigation-link-thumbnail">
									# IF C_PREVIOUS_HAS_THUMBNAIL #<img src="# IF C_PREVIOUS_PTR #{PATH_TO_ROOT}# ENDIF #{PREVIOUS_THUMBNAIL}" alt="{PREVIOUS_ITEM_TITLE}" /># ENDIF #
									<figcaption><i class="fa fa-arrow-circle-left"></i> {PREVIOUS_ITEM_TITLE}</figcaption>
								</figure>
							</a>
						</span>
					# ENDIF #
					# IF C_NEXT_ITEM #
						<span class="navigation-link-next">
							<a href="{U_NEXT_ITEM}">
								<figure class="navigation-link-thumbnail">
									# IF C_NEXT_HAS_THUMBNAIL #<img src="# IF C_NEXT_PTR #{PATH_TO_ROOT}# ENDIF #{NEXT_THUMBNAIL}" alt="{NEXT_ITEM_TITLE}" /># ENDIF #
									<figcaption>{NEXT_ITEM_TITLE} <i class="fa fa-arrow-circle-right"></i></figcaption>
								</figure>
							</a>
						</span>
					# ENDIF #
					<div class="spacer"></div>
				</div>
			# ENDIF #

			# IF C_NOTATION_ENABLED #
				<hr />
				<div class="left small">
					{KERNEL_NOTATION}
				</div>
				<div class="spacer"></div>
			# ENDIF #

			# IF C_COMMENTS_ENABLED #
				<hr />
				# INCLUDE COMMENTS #
			# ENDIF #
		</aside>
		<footer></footer>
	</article>
	<footer></footer>
</section>
