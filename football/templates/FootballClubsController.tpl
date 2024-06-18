<section id="module-football" class="several-items">
	<header class="section-header">
		<h1 class="flex-between">
			{@football.clubs}
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
                <div class="" style="columns: 6">
                    # START clubs #
                        <article class="flex-team flex-left">
                            <img src="{clubs.U_LOGO}" alt="{clubs.FULL_NAME}">
                            <span><a href="{clubs.U_CLUB}" class="offload">{clubs.NAME}</a></span>
                        </article>
                    # END clubs #
                </div>
			</div>
		</div>
	# ENDIF #
	<footer></footer>
</section>
