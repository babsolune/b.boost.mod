<!-- === ScmLobbyProvider === -->
<link id="scm-provider" rel="stylesheet" href="{PATH_TO_ROOT}/modules/scm/templates/scm.css" type="text/css" media="screen, print" />
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
                                <div class="cell cell-game">
                                    <div class="flex-between small cell-pad">
                                        <div class="text-italic sm-width-pc-50 align-center">
                                            <span class="text-strong"><a class="offload" href="{next_items.U_GAME_CATEGORY}">{next_items.GAME_CATEGORY}</a></span>
                                            # IF next_items.C_IS_SUB #<a class="offload d-block smaller" href="{next_items.U_MASTER_EVENT}">{next_items.MASTER_EVENT}</a># ENDIF #
                                        </div>
                                        <div class="md-width-pc-50 controls">
                                            <span>{@scm.day.short}{next_items.GAME_CLUSTER} : {next_items.GAME_DATE_DAY}/{next_items.GAME_DATE_MONTH}/{next_items.YEAR} {next_items.GAME_DATE_HOUR}:{next_items.GAME_DATE_MINUTE}</span>
                                            <a class="offload" href="{next_items.U_EVENT}">{next_items.GAME_DIVISION}</a>
                                        </div>
                                    </div>
                                    # IF next_items.C_LATE #<div class="bgc notice smaller text-italic align-center">{@scm.game.late}</div># ENDIF #
                                    <div class="flex-between cell-pad# IF next_items.C_EXEMPT # bgc notice# ENDIF #">
                                        <div class="game-team home-team flex-team flex-right sm-width-pc-40">
                                            <span>
                                                # IF next_items.C_HOME_EXEMPT #
                                                    {next_items.HOME_TEAM}
                                                # ELSE #
                                                    <a
                                                        href="{next_items.U_HOME_CALENDAR}"
                                                        aria-label="{@scm.club.see.calendar}"
                                                        class="offload# IF next_items.C_HOME_FAV # text-strong# ENDIF ## IF next_items.HOME_FORFEIT # warning# ENDIF #">
                                                        {next_items.HOME_TEAM}
                                                    </a>
                                                # ENDIF #
                                            </span>
                                        </div>
                                        <div class="align-center hidden-small-screens sm-width-pc-20">&nbsp;|&nbsp;</div>
                                        <div class="game-team away-team flex-team flex-left sm-width-pc-40">
                                            <span>
                                                # IF next_items.C_AWAY_EXEMPT #
                                                    {next_items.AWAY_TEAM}
                                                # ELSE #
                                                    <a
                                                        href="{next_items.U_AWAY_CALENDAR}"
                                                        aria-label="{@scm.club.see.calendar}"
                                                        class="offload# IF next_items.C_AWAY_FAV # text-strong# ENDIF ## IF next_items.AWAY_FORFEIT # warning# ENDIF #">
                                                        {next_items.AWAY_TEAM}
                                                    </a>
                                                # ENDIF #
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            # END next_items #
                        # ELSE #
                            <div class="cell-content"><span class="message-helper bgc notice">{@scm.no.games}</span></div>
                        # ENDIF #
                    </article>
                    <article class="cell">
                        <header class="cell-header align-center"><h4 class="cell-name">{@scm.prev.games}</h4></header>
                        # IF C_PREV_ITEMS #
                            # START prev_items #
                                <div class="cell cell-pad">
                                    <div class="flex-between bgc-sub cell-pad">
                                        <div class="text-italic md-width-pc-50 align-center">
                                            <span class="text-strong">{prev_items.GAME_CATEGORY}</span>
                                            # IF prev_items.C_IS_SUB #<a class="offload d-block smaller" href="{prev_items.U_MASTER_EVENT}">{prev_items.MASTER_EVENT}</a># ENDIF #
                                        </div>
                                        <div class="smaller md-width-pc-50 align-center">
                                            <span>{@scm.day.short}{prev_items.GAME_CLUSTER} : {prev_items.GAME_DATE_DAY}/{prev_items.GAME_DATE_MONTH}/{prev_items.YEAR} {prev_items.GAME_DATE_HOUR}:{prev_items.GAME_DATE_MINUTE}</span>
                                            <a class="offload d-block" href="{prev_items.U_EVENT}">{prev_items.GAME_DIVISION}</a>
                                        </div>
                                    </div>
                                    # IF prev_items.C_LATE #<div class="bgc notice smaller text-italic align-center" colspan="3">{@scm.game.late}</div># ENDIF #
                                    <div class="flex-between category-{prev_items.CATEGORY_ID} cell-pad# IF prev_items.C_EXEMPT # bgc notice# ENDIF #">
                                        <div class="game-team home-team flex-team flex-right sm-width-pc-80 align-right# IF prev_items.C_HOME_EXEMPT # small text-italic# ENDIF #">
                                            <span>
                                                # IF prev_items.C_HOME_EXEMPT #
                                                    {prev_items.HOME_TEAM}
                                                # ELSE #
                                                    <a
                                                        href="{prev_items.U_HOME_CALENDAR}"
                                                        aria-label="{@scm.club.see.calendar}# IF prev_items.HOME_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF prev_items.HOME_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                                        class="offload# IF prev_items.C_HOME_FAV # text-strong# ENDIF ## IF prev_items.HOME_FORFEIT # warning# ENDIF ## IF prev_items.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                                        {prev_items.HOME_TEAM}
                                                    </a>
                                                # ENDIF #
                                            </span>
                                        </div>
                                        # IF prev_items.C_STATUS #
                                            <div class="md-width-pc-20 small text-italic warning align-center">{prev_items.STATUS}</div>
                                        # ELSE #
                                            <div class="md-width-pc-20 align-center"># IF prev_items.C_HAS_SCORE #{prev_items.HOME_SCORE} - {prev_items.AWAY_SCORE}# ENDIF #</div>
                                        # ENDIF #
                                        <div class="md-width-pc-40 align-left# IF prev_items.C_AWAY_EXEMPT # small text-italic# ENDIF #">
                                            <span>
                                                # IF prev_items.C_AWAY_EXEMPT #
                                                    {prev_items.AWAY_TEAM}
                                                # ELSE #
                                                    <a
                                                        href="{prev_items.U_AWAY_CALENDAR}"
                                                        aria-label="{@scm.club.see.calendar}# IF prev_items.AWAY_FORFEIT # - {@scm.game.event.forfeit}# ENDIF ## IF prev_items.AWAY_GENERAL_FORFEIT # - {@scm.game.event.general.forfeit}# ENDIF #"
                                                        class="offload# IF prev_items.C_AWAY_FAV # text-strong# ENDIF ## IF prev_items.AWAY_FORFEIT # warning# ENDIF ## IF prev_items.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                                        {prev_items.AWAY_TEAM}
                                                    </a>
                                                # ENDIF #
                                            </span>
                                        </div>
                                    </div>
                                </div>
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
<script src="{PATH_TO_ROOT}/modules/scm/templates/js/scm.width.js"></script>
<script>
    const styleLink = document.getElementById('scm-provider');
    if (styleLink) {
        styleLink.remove();
        document.head.appendChild(styleLink);
    }
</script>
