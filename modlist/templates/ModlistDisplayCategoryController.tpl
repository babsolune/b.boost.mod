<section id="module-modlist">
	<header>
		<h1>
			<a href="${relative_url(SyndicationUrlBuilder::rss('modlist', CATEGORY_ID))}" title="${LangLoader::get_message('syndication', 'common')}"><i class="fa fa-syndication"></i></a>
			# IF C_PENDING #{@modlist.pending.items}# ELSE #{@modlist.module.title}# IF NOT C_ROOT_CATEGORY # - {CATEGORY_NAME}# ENDIF ## ENDIF # # IF C_CATEGORY ## IF IS_ADMIN #<a href="{U_EDIT_CATEGORY}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit smaller"></i></a># ENDIF ## ENDIF #
		</h1>
		# IF C_CATEGORY_DESCRIPTION #
			<div class="cat-description">
				# IF NOT C_ROOT_CATEGORY #
					# IF C_DISPLAY_CAT_ICONS #
						# IF C_CATEGORY_IMAGE #
							<img itemprop="thumbnailUrl" src="{CATEGORY_IMAGE}" alt="{CATEGORY_NAME}" />
						# ENDIF #
					# ENDIF #
				# ENDIF #
				{CATEGORY_DESCRIPTION}
			</div>
		# ENDIF #
	</header>


	# IF C_NO_ITEM_AVAILABLE #
		# IF NOT C_HIDE_NO_ITEM_MESSAGE #
		<div class="center">
			${LangLoader::get_message('no_item_now', 'common')}
		</div>
		# ENDIF #
	# ELSE #
		# IF C_ITEMS_SORT_FILTERS #
			# INCLUDE FORM #
		# ENDIF #
		<div class="spacer"></div>
		# IF C_TABLE #
			<table id="table">
				<thead>
					<tr>
						<th>${LangLoader::get_message('title', 'main')}</th>
						<th>${LangLoader::get_message('author', 'common')}</th>
						<th>${LangLoader::get_message('date', 'date-common')}</th>
						# IF C_MODERATION #
							<th>${LangLoader::get_message('administrator_alerts_action', 'admin')}</th>
						# ENDIF #
					</tr>
				</thead>
				<tbody>
					# START items #
					<tr>
						<td><a itemprop="url" href="{items.U_ITEM}"><span itemprop="name">{items.TITLE}</span></a></td>
						# IF items.C_DISPLAYED_AUTHOR #
							<td>
								# IF items.C_CUSTOM_AUTHOR_NAME #
									{items.CUSTOM_AUTHOR_NAME}
								# ELSE #
									# IF items.C_AUTHOR_EXIST #<a itemprop="author" href="{items.U_AUTHOR}" class="{items.USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{items.USER_GROUP_COLOR}"# ENDIF #>{items.PSEUDO}</a># ELSE #{items.PSEUDO}# ENDIF #
								# ENDIF #

							</td>
						# ENDIF #
						<td>
							<time datetime="# IF NOT items.C_DIFFERED #{items.DATE_ISO8601}# ELSE #{items.PUBLICATION_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished"># IF NOT items.C_DIFFERED #{items.DATE}# ELSE #{items.PUBLICATION_START_DATE}# ENDIF #</time>
						</td>
						# IF C_MODERATION #
							<td>
								# IF items.C_EDIT #
									<a href="{items.U_EDIT_ITEM}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit"></i></a>
								# ENDIF #
								# IF items.C_DELETE #
									<a href="{items.U_DELETE_ITEM}" title="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="fa fa-delete"></i></a>
								# ENDIF #
							</td>
						# ENDIF #
					</tr>
					# END items #
				</tbody>
			</table>

		# ELSE #

			<div class="elements-container# IF C_SEVERAL_COLUMNS # columns-{COLUMNS_NUMBER}# ENDIF#">
				# START items #
					<article id="modlist-items-{items.ID}" class="modlist-items several-items# IF C_MOSAIC # block# ENDIF ## IF items.C_NEW_CONTENT # new-content# ENDIF #" itemscope="itemscope" itemtype="http://schema.org/CreativeWork">
						<header>
							<h2>
								<a itemprop="url" href="{items.U_ITEM}"><span itemprop="name">{items.TITLE}</span></a>
								<span class="actions">
									# IF items.C_EDIT #
										<a href="{items.U_EDIT_ITEM}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit"></i></a>
									# ENDIF #
									# IF items.C_DELETE #
										<a href="{items.U_DELETE_ITEM}" title="${LangLoader::get_message('delete', 'common')}" data-confirmation="delete-element"><i class="fa fa-delete"></i></a>
									# ENDIF #
								</span>
							</h2>

							<div class="more">
								# IF items.C_DISPLAYED_AUTHOR #
									${LangLoader::get_message('by', 'common')}
									# IF items.C_CUSTOM_AUTHOR_NAME #
										{items.CUSTOM_AUTHOR_NAME}
									# ELSE #
										# IF items.C_AUTHOR_EXIST #<a itemprop="author" href="{items.U_AUTHOR}" class="{items.USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{items.USER_GROUP_COLOR}"# ENDIF #>{items.PSEUDO}</a># ELSE #{items.PSEUDO}# ENDIF #,
									# ENDIF #
								# ENDIF #
								${LangLoader::get_message('the', 'common')} <time datetime="# IF NOT items.C_DIFFERED #{items.DATE_ISO8601}# ELSE #{items.PUBLICATION_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished"># IF NOT items.C_DIFFERED #{items.DATE}# ELSE #{items.PUBLICATION_START_DATE}# ENDIF #</time>
								${TextHelper::lcfirst(LangLoader::get_message('in', 'common'))} <a itemprop="about" href="{items.U_CATEGORY}">{items.CATEGORY_NAME}</a>
							</div>

							<meta itemprop="url" content="{items.U_ITEM}">
							<meta itemprop="description" content="${escape(items.DESCRIPTION)}"/>
							<meta itemprop="discussionUrl" content="{items.U_COMMENTS}">
							<meta itemprop="interactionCount" content="{items.COMMENTS_NUMBER} UserComments">

						</header>

						<div class="content">
							# IF items.C_HAS_THUMBNAIL #<a href="{items.U_ITEM}"><img itemprop="thumbnailUrl" src="{items.THUMBNAIL}" alt="{items.TITLE}" /></a># ENDIF #
							<div itemprop="text">{items.DESCRIPTION}# IF items.C_READ_MORE #... <a href="{items.U_ITEM}" class="read-more">[${LangLoader::get_message('read-more', 'common')}]</a># ENDIF #</div>
						</div>

						# IF items.C_SOURCES #
						<div class="spacer"></div>
						<aside>
						<div id="modlist-sources-container">
							<span>${LangLoader::get_message('form.sources', 'common')}</span> :
							# START items.sources #
							<a itemprop="isBasedOnUrl" href="{items.sources.URL}" class="small">{items.sources.NAME}</a># IF items.sources.C_SEPARATOR #, # ENDIF #
							# END items.sources #
						</div>
						</aside>
						# ENDIF #

						<footer></footer>
					</article>
				# END items #
			</div>
		# ENDIF #
	# ENDIF #
		<div class="spacer"></div>
	<footer># IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #</footer>
</section>
