<div class="sub-section" style="order: {MODULE_POSITION};">
	<div class="content-container">
		<article id="{MODULE_NAME}-panel">
			<header class="module-header flex-between">
				<h2>{L_MODULE_TITLE}</h2>
				<div class="controls align-right">
					<a class="offload" href="{PATH_TO_ROOT}/{MODULE_NAME}" aria-label="{@lobby.see.module}"><i class="fa fa-share-square" aria-hidden="true"></i></a>
				</div>
			</header>
			<div class="content">
				<div class="cell-flex cell-columns-2 cell-tile">
                    <article class="cell">
                        <header class="cell-header align-center"><h4 class="cell-name">{@scm.next.games}</h4></header>
                        # IF C_NEXT_ITEMS #
                            # START next_items #
                                next plop
                            # END next_items #
                        # ELSE #
                            <div class="cell-content"><span class="message-helper bgc notice">{@scm.no.games}</span></div>
                        # ENDIF #
                    </article>
                    <article class="cell">
                        <header class="cell-header align-center"><h4 class="cell-name">{@scm.prev.games}</h4></header>
                        # IF C_PREV_ITEMS #
                            # START prev_items #
                                prev plop
                            # END prev_items #
                        # ELSE #
                            <div class="cell-content"><span class="message-helper bgc notice">{@scm.no.games}</span></div>
                        # ENDIF #
                    </article>
                </div>
			</div>
		</article>
	</div>
</div>
