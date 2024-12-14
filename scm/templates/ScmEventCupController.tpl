# IF C_HAS_GAMES #
    <article class="groups">
        <header class="article-header"><h3>{@scm.teams}</h3></header>
        <div class="content">
            # START team_groups #
                <div class="flex-team">
                    <div class="flex-team-container">
                        # START team_groups.teams #
                            <span class="pinned link-color">
                                <img src="{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                <span><a href="{team_groups.teams.U_CLUB}" class="offload" aria-label="{@scm.club.see.infos}">{team_groups.teams.TEAM_NAME}</a></span>
                            </span>
                        # END team_groups.teams #
                        </div>
                </div>
            # END team_groups #
        </div>
    </article>
    <article class="games">
        <header class="article-header flex-between">
            <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
        </header>
        # IF C_ONE_DAY #<p>{ONE_DAY_DATE}</p># ENDIF #
        <p><h4>{@scm.games.brackets.stage}</h4></p>
        # START matchrounds #
            <h5>{matchrounds.L_MATCHROUND}</h5>
            <div class="cell-flex cell-columns-2">
                # START matchrounds.dates #
                    <div>
                        # IF NOT C_ONE_DAY #
                            <h6>{matchrounds.dates.DATE}</h6>
                        # ENDIF #
                        <div class="cell-flex cell-columns-2">
                            # START matchrounds.dates.brackets #
                                <div id="{matchrounds.dates.brackets.GAME_ID}" class="cell game-container" data-scroll="{matchrounds.dates.brackets.GAME_DATE_TIMESTAMP}">
                                    <div class="game-details small text-italic">
                                        # IF C_PLAYGROUND #<span>{matchrounds.dates.brackets.PLAYGROUND}</span># ENDIF #
                                        <span>
                                            # IF matchrounds.dates.brackets.C_IS_LIVE #
                                                <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                                            # ELSE #
                                                {matchrounds.dates.brackets.GAME_DATE_HOUR_MINUTE}
                                            # ENDIF #
                                        </span>
                                        <span>{matchrounds.dates.brackets.GAME_ID}</span>
                                    </div>
                                    <div  class="id-{matchrounds.dates.brackets.HOME_ID} game-team game-home# IF matchrounds.dates.brackets.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF matchrounds.dates.brackets.C_HOME_WIN # style="background-color: {matchrounds.dates.brackets.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{matchrounds.dates.brackets.GAME_ID} home-team">
                                            # IF matchrounds.dates.brackets.HOME_ID #
                                                <div class="flex-team flex-left">
                                                    # IF matchrounds.dates.brackets.C_HAS_HOME_LOGO #<img src="{matchrounds.dates.brackets.HOME_LOGO}" alt="{matchrounds.dates.brackets.HOME_TEAM}"># ENDIF #
                                                    <span>{matchrounds.dates.brackets.HOME_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score home-score md-width-px-50">{matchrounds.dates.brackets.HOME_SCORE}# IF matchrounds.dates.brackets.C_HAS_PEN # <span class="small">({matchrounds.dates.brackets.HOME_PEN})</span># ENDIF #</div>
                                    </div>
                                    <div class="id-{matchrounds.dates.brackets.AWAY_ID} game-team game-away# IF matchrounds.dates.brackets.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF matchrounds.dates.brackets.C_AWAY_WIN # style="background-color: {matchrounds.dates.brackets.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{matchrounds.dates.brackets.GAME_ID} away-team">
                                            # IF matchrounds.dates.brackets.AWAY_ID #
                                                <div class="flex-team flex-left">
                                                    # IF matchrounds.dates.brackets.C_HAS_AWAY_LOGO #<img src="{matchrounds.dates.brackets.AWAY_LOGO}" alt="{matchrounds.dates.brackets.AWAY_TEAM}"># ENDIF #
                                                    <span>{matchrounds.dates.brackets.AWAY_TEAM}</span>
                                                </div>
                                            # ENDIF #
                                        </div>
                                        <div class="game-score away-score md-width-px-50">{matchrounds.dates.brackets.AWAY_SCORE}# IF matchrounds.dates.brackets.C_HAS_PEN # <span class="small">({matchrounds.dates.brackets.AWAY_PEN})</span># ENDIF #</div>
                                    </div>
                                </div>
                            # END matchrounds.dates.brackets #
                        </div>
                    </div>
                # END matchrounds.dates #
            </div>
        # END matchrounds #
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>