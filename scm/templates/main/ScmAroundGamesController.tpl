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
					<div class="message-helper bgc notice">{@scm.message.no.games}</div>
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
                        <div class="cell-flex cell-columns-2 cell-around">
                            <div class="cell">
                                <header class="cell-header"><h2 class="cell-name">{@scm.mini.next}</h2></header>
                                # IF C_NEXT_ITEMS #
                                    # INCLUDE NEXT_ITEMS #
                                # ELSE #
                                    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
                                # ENDIF #
                            </div>
                            <div class="cell">
                                <header class="cell-header"><h2 class="cell-name">{@scm.mini.prev}</h2></header>
                                # IF C_PREV_ITEMS #
                                    # INCLUDE PREV_ITEMS #
                                # ELSE #
                                    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
                                # ENDIF #
                            </div>
                        </div>
					</div>
				</article>

			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>
