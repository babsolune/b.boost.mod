<section id="module-wiki" class="several-items">
	<header class="section-header">
		<div class="controls align-right">
			<a class="offload" href="${relative_url(SyndicationUrlBuilder::rss('wiki', ID_CAT))}" aria-label="{@common.syndication}"><i class="fa fa-rss warning" aria-hidden="true"></i></a>
			# IF NOT C_ROOT_CATEGORY #{MODULE_NAME}# ENDIF #
			# IF C_DISPLAY_REORDER_LINK #
				<a class="offload" href="{U_REORDER_ITEMS}" aria-label="{@items.reorder}"><i class="fa fa-fw fa-exchange-alt" aria-hidden="true"></i></a>
			# ENDIF #
			# IF C_CATEGORY ## IF IS_ADMIN #<a class="offload" href="{U_EDIT_CATEGORY}" aria-label="{@common.edit}"><i class="far fa-edit" aria-hidden="true"></i></a># ENDIF ## ENDIF #
		</div>
		<h1>
			{MODULE_NAME}
		</h1>
	</header>
    # IF C_HOMEPAGE #
        # IF C_ROOT_CATEGORY_DESCRIPTION #
            <div class="sub-section">
                <div class="content-container">{ROOT_CATEGORY_DESCRIPTION}</div>
            </div>
        # ENDIF #
    # ENDIF #
    <div class="sub-section">
        <div class="content-container">
            <div id="index-nav" class="index-list">
                # START categories #
                    <div
                            class="cell"
                            data-id="{categories.CATEGORY_ID}"
                            data-p-id="{categories.CATEGORY_PARENT_ID}"
                            data-order-id="{categories.CATEGORY_SUB_ORDER}">
                        <span><a class="offload" href="{categories.U_CATEGORY}" aria-label="{categories.CATEGORY_NAME}">{categories.CATEGORY_NAME}</a># IF categories.C_DISPLAY_DESCRIPTION # | <span class="text-italic small">{categories.CATEGORY_DESCRIPTION}</span># ENDIF #</span>
                    </div>
                # END categories #
            </div>
        </div>
    </div>
	<footer></footer>
</section>
<script src="{PATH_TO_ROOT}/wiki/templates/js/wiki# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
