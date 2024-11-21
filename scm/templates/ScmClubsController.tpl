<section id="module-scm" class="several-items">
	<header class="section-header">
		<h1 class="flex-between">
			{@scm.clubs}
		</h1>
	</header>

	# IF C_NO_ITEM #
		<div class="sub-section">
			<div class="content-container">
				<div class="content">
					<div class="message-helper bgc notice">{@common.no.item.now}</div>
				</div>
			</div>
		</div>
	# ELSE #
		<div class="sub-section">
			<div class="content-container">
                <div class="columns-6">
                    # START clubs #
                        <article class="flex-team flex-left mb">
                            # IF clubs.C_HAS_SHIELD #
                                # IF clubs.C_HAS_LOGO #
                                    <img src="{clubs.U_LOGO}" alt="{clubs.FULL_NAME}">
                                # ELSE #
                                    # IF clubs.C_HAS_FLAG #
                                        <img src="{clubs.U_FLAG}" alt="{clubs.FULL_NAME}">
                                    # ENDIF #
                                # ENDIF #
                            # ENDIF #
                            <span><a href="{clubs.U_CLUB}" aria-label="{clubs.FULL_NAME}" class="offload">{clubs.NAME}</a></span>
                        </article>
                    # END clubs #
                </div>
			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>
