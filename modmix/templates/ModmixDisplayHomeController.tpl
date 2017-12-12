<section id="module-modmix">
	<header>
		<h1>
			<a href="${relative_url(SyndicationUrlBuilder::rss('modmix', CATEGORY_ID))}" title="${LangLoader::get_message('syndication', 'common')}"><i class="fa fa-syndication"></i></a>
			# IF C_PENDING #{@modmix.pending.items}# ELSE #{@modmix.module.title}# ENDIF # # IF IS_ADMIN #<a href="{U_CONFIG}" title="${LangLoader::get_message('edit', 'common')}"><i class="fa fa-edit smaller"></i></a># ENDIF #
		</h1>
		# IF C_CATEGORY_DESCRIPTION #
			<div class="cat-description">
				{CATEGORY_DESCRIPTION}
			</div>
		# ENDIF #
	</header>

	# IF C_TABLE #
		<ul class="category-sort">
			# START categories #
				<li class="modmix-category" data-cat-id="{categories.ID}" data-parent-id="{categories.ID_PARENT}" data-c-order="{categories.SUB_ORDER}">
					<a class="category-id# IF categories.C_LEVEL_ONE_CATEGORY # level-one-cat# ENDIF #" href="{categories.U_CATEGORY}" title="{categories.CATEGORY_NAME}">{categories.CATEGORY_NAME}</a> <span class="actions">{categories.ID_PARENT} | {categories.SUB_ORDER} | {categories.ID}</span>
					# START categories.items #
						<ul>
							<li class="modmix-item-title"><a itemprop="url" href="{categories.items.U_ITEM}"><span itemprop="name">{categories.items.TITLE}</span></a></li>
							<li class="modmix-item-author">
								# IF categories.items.C_DISPLAYED_AUTHOR #
									${LangLoader::get_message('by', 'common')}
									# IF categories.items.C_CUSTOM_AUTHOR_NAME #
										{categories.items.CUSTOM_AUTHOR_NAME}
									# ELSE #
										# IF categories.items.C_AUTHOR_EXIST #<a itemprop="author" href="{categories.items.U_AUTHOR}" class="{categories.items.USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{categories.items.USER_GROUP_COLOR}"# ENDIF #>{categories.items.PSEUDO}</a># ELSE #{categories.items.PSEUDO}# ENDIF #
									# ENDIF #
								# ENDIF #
							</li>
							<li class="modmix-item-date">
								<time class="item-date" datetime="# IF NOT categories.items.C_DIFFERED #{categories.items.DATE_ISO8601}# ELSE #{categories.items.PUBLICATION_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished"># IF NOT categories.items.C_DIFFERED #{categories.items.DATE}# ELSE #{categories.items.PUBLICATION_START_DATE}# ENDIF #</time>
							</li>
						</ul>
					# END categories.items #
				</li>

			# END categories #
		</ul>
	# ELSE #
		<div class="elements-container# IF C_MOSAIC # columns-{COLUMNS_NUMBER}# ENDIF #">
			# START categories #
				<article class="category-tr# IF C_MOSAIC # block# ENDIF #" data-cat-id="{categories.ID}" data-parent-id="{categories.ID_PARENT}" data-c-order="{categories.SUB_ORDER}">
					<header>
						<h2><a href="{categories.U_CATEGORY}" title="{categories.CATEGORY_NAME}">{categories.CATEGORY_NAME}</a></h2>
					</header>
					<div class="content">
						<ul>
							# START categories.items #
								<meta itemprop="url" content="{categories.items.U_ITEM}">
								<meta itemprop="description" content="${escape(categories.items.DESCRIPTION)}"/>
								<meta itemprop="discussionUrl" content="{categories.items.U_COMMENTS}">
								<meta itemprop="interactionCount" content="{categories.items.COMMENTS_NUMBER} UserComments">
								<li>
									<meta itemprop="url" content="{categories.items.U_ITEM}">
									<meta itemprop="description" content="${escape(categories.items.DESCRIPTION)}"/>
									<span class"item-date"><time datetime="# IF NOT categories.items.C_DIFFERED #{categories.items.DATE_ISO8601}# ELSE #{categories.items.PUBLICATION_START_DATE_ISO8601}# ENDIF #" itemprop="datePublished"># IF NOT categories.items.C_DIFFERED #{categories.items.DATE}# ELSE #{categories.items.PUBLICATION_START_DATE}# ENDIF #</time></span>
									<a itemprop="url" href="{categories.items.U_ITEM}"><span itemprop="name">{categories.items.TITLE}.</span></a>
									# IF categories.items.C_DISPLAYED_AUTHOR #
										${LangLoader::get_message('by', 'common')}
										# IF categories.items.C_CUSTOM_AUTHOR_NAME #
											{categories.items.CUSTOM_AUTHOR_NAME}
										# ELSE #
											# IF categories.items.C_AUTHOR_EXIST #<a itemprop="author" href="{categories.items.U_AUTHOR}" class="{categories.items.USER_LEVEL_CLASS}" # IF C_USER_GROUP_COLOR # style="color:{categories.items.USER_GROUP_COLOR}"# ENDIF #>{categories.items.PSEUDO}</a># ELSE #{categories.items.PSEUDO}# ENDIF #
										# ENDIF #
									# ENDIF #
								</li>
							# END categories.items #
						</ul>
					</div>
				</article>
			# END categories #
		</div>
	# ENDIF #


	# IF C_NO_ITEM_AVAILABLE #
		# IF NOT C_HIDE_NO_ITEM_MESSAGE #
		<div class="center">
			${LangLoader::get_message('no_item_now', 'common')}
		</div>
		# ENDIF #
	# ENDIF #
		<div class="spacer"></div>
	<footer># IF C_PAGINATION # # INCLUDE PAGINATION # # ENDIF #</footer>
</section>

<script>
<!--
	$('.category-sort').each(function() {
		$(this).html($(this).children('.modmix-category').sort(function(a,b) {
			return ($(b).data('parent-id')) == ($(a).data('cat-id')) ? 1 : -1
		}));
	});
-->
</script>
