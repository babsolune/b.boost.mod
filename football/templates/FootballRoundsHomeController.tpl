# IF C_HAS_MATCHES #
    <article class="groups">
        <header class="article-header"><h3>{@football.groups}</h3></header>
        <div class="cell-flex">
            <div class="responsive-table">
                <table class="table bordered-table">
                    <thead>
                        <tr>
                            # START team_groups #
                                <th class="bgc visitor">{@football.group} {team_groups.GROUP}</th>
                            # END team_groups #
                            </tr>
                    </thead>
                    <tbody>
                        <tr>
                            # START team_groups #
                                <td>
                                    # START team_groups.teams #
                                        <div class="flex-team">
                                            <img src="{PATH_TO_ROOT}/{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                            <span>{team_groups.teams.TEAM_NAME}</span>
                                        </div>
                                    # END team_groups.teams #
                                </td>
                            # END team_groups #
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </article>
    # IF C_ONE_DAY #
        <article class="matches">
            <header class="article-header">
                <h3>{@football.matches.groups.stage}</h3>
            </header>
            <p><h4>{@football.matches.groups.stage}</h4></p>
            <div class="cell-flex cell-columns-4">
                # START groups #
                    <div id="{groups.MATCH_ID}" class="cell game-match">
                        <div class="game-details small text-italic">
                            <span>{groups.PLAYGROUND}</span>
                            <span>
                                # IF groups.C_IS_LIVE #
                                    <span class="blink pinned bgc-full notice">{@football.is.live}</span>
                                # ELSE #
                                    {groups.MATCH_DATE_HOUR_MINUTE}
                                # ENDIF #
                            </span>
                            <a href="{groups.U_GROUP}" class="offload">{@football.group} {groups.GROUP_NAME}</a>
                        </div>
                        <div  class="id-{groups.HOME_ID} game-team game-home# IF groups.C_HOME_FAV # text-strong# ENDIF #"
                                # IF groups.C_HOME_WIN # style="background-color: {groups.WIN_COLOR}"# ENDIF #>
                            <div class="home-{groups.MATCH_ID} home-team">
                                # IF groups.HOME_ID #
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{groups.HOME_LOGO}" alt="{groups.HOME_TEAM}">
                                        <span>{groups.HOME_TEAM}</span>
                                    </div>
                                # ENDIF #
                            </div>
                            <div class="game-score home-score width-px-50">{groups.HOME_SCORE}# IF groups.C_HAS_PEN # <span class="small">({groups.HOME_PEN})</span># ENDIF #</div>
                        </div>
                        <div class="id-{groups.AWAY_ID} game-team game-away# IF groups.C_AWAY_FAV # text-strong# ENDIF #"
                                # IF groups.C_AWAY_WIN # style="background-color: {groups.WIN_COLOR}"# ENDIF #>
                            <div class="away-{groups.MATCH_ID} away-team">
                                # IF groups.AWAY_ID #
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{groups.AWAY_LOGO}" alt="{groups.AWAY_TEAM}">
                                        <span>{groups.AWAY_TEAM}</span>
                                    </div>
                                # ENDIF #
                            </div>
                            <div class="game-score away-score width-px-50">{groups.AWAY_SCORE}# IF groups.C_HAS_PEN # <span class="small">({groups.AWAY_PEN})</span># ENDIF #</div>
                        </div>
                    </div>
                # END groups #
            </div>
            <p><h4>{@football.matches.brackets.stage}</h4></p>
            <div class="cell-flex cell-columns-4">
                # START brackets #
                    <div id="{brackets.MATCH_ID}" class="cell game-match">
                        <div class="game-details small text-italic">
                            <span>{brackets.PLAYGROUND}</span>
                            <span>
                                # IF brackets.C_IS_LIVE #
                                    <span class="blink pinned bgc-full notice">{@football.is.live}</span>
                                # ELSE #
                                    {brackets.MATCH_DATE_HOUR_MINUTE}
                                # ENDIF #
                            </span>
                            <span>{brackets.MATCH_ID}</span>
                        </div>
                        <div  class="id-{brackets.HOME_ID} game-team game-home# IF brackets.C_HOME_FAV # text-strong# ENDIF #"
                                # IF brackets.C_HOME_WIN # style="background-color: {brackets.WIN_COLOR}"# ENDIF #>
                            <div class="home-{brackets.MATCH_ID} home-team">
                                # IF brackets.HOME_ID #
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{brackets.HOME_LOGO}" alt="{brackets.HOME_TEAM}">
                                        <span>{brackets.HOME_TEAM}</span>
                                    </div>
                                # ENDIF #
                            </div>
                            <div class="game-score home-score width-px-50">{brackets.HOME_SCORE}# IF brackets.C_HAS_PEN # <span class="small">({brackets.HOME_PEN})</span># ENDIF #</div>
                        </div>
                        <div class="id-{brackets.AWAY_ID} game-team game-away# IF brackets.C_AWAY_FAV # text-strong# ENDIF #"
                                # IF brackets.C_AWAY_WIN # style="background-color: {brackets.WIN_COLOR}"# ENDIF #>
                            <div class="away-{brackets.MATCH_ID} away-team">
                                # IF brackets.AWAY_ID #
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{brackets.AWAY_LOGO}" alt="{brackets.AWAY_TEAM}">
                                        <span>{brackets.AWAY_TEAM}</span>
                                    </div>
                                # ENDIF #
                            </div>
                            <div class="game-score away-score width-px-50">{brackets.AWAY_SCORE}# IF brackets.C_HAS_PEN # <span class="small">({brackets.AWAY_PEN})</span># ENDIF #</div>
                        </div>
                    </div>
                # END brackets #
            </div>
        </article>
    # ELSE #
        <article class="matches">
            <header class="article-header">
                <h3>{@football.calendar}</h3>
            </header>
            # START matchdays #
                <p>{matchdays.DATE}</p>
                <div class="cell-flex cell-columns-4">
                    # START matchdays.groups #
                        <div id="{matchdays.groups.MATCH_ID}" class="cell game-match">
                            <div class="game-details small text-italic">
                                <span>{matchdays.groups.PLAYGROUND}</span>
                                <span>
                                    # IF matchdays.groups.C_IS_LIVE #
                                        <span class="blink pinned bgc-full notice">{@football.is.live}</span>
                                    # ELSE #
                                        {matchdays.groups.MATCH_DATE_HOUR_MINUTE}
                                    # ENDIF #
                                </span>
                                <a href="{matchdays.groups.U_GROUP}" class="offload">
                                    # IF C_HAT_RANKING #
                                        {@football.day} {matchdays.groups.DAY_NAME}
                                    # ELSE #
                                        {@football.group} {matchdays.groups.GROUP_NAME}
                                    # ENDIF #
                                </a>
                            </div>
                            <div  class="id-{matchdays.groups.HOME_ID} game-team game-home# IF matchdays.groups.C_HOME_FAV # text-strong# ENDIF #"
                                    # IF matchdays.groups.C_HOME_WIN # style="background-color: {matchdays.groups.WIN_COLOR}"# ENDIF #>
                                <div class="home-{matchdays.groups.MATCH_ID} home-team">
                                    # IF matchdays.groups.HOME_ID #
                                        <div class="flex-team flex-left">
                                            <img src="{PATH_TO_ROOT}/{matchdays.groups.HOME_LOGO}" alt="{matchdays.groups.HOME_TEAM}">
                                            <span>{matchdays.groups.HOME_TEAM}</span>
                                        </div>
                                    # ENDIF #
                                </div>
                                <div class="game-score home-score width-px-50">{matchdays.groups.HOME_SCORE}# IF matchdays.groups.C_HAS_PEN # <span class="small">({matchdays.groups.HOME_PEN})</span># ENDIF #</div>
                            </div>
                            <div class="id-{matchdays.groups.AWAY_ID} game-team game-away# IF matchdays.groups.C_AWAY_FAV # text-strong# ENDIF #"
                                    # IF matchdays.groups.C_AWAY_WIN # style="background-color: {matchdays.groups.WIN_COLOR}"# ENDIF #>
                                <div class="away-{matchdays.groups.MATCH_ID} away-team">
                                    # IF matchdays.groups.AWAY_ID #
                                        <div class="flex-team flex-left">
                                            <img src="{PATH_TO_ROOT}/{matchdays.groups.AWAY_LOGO}" alt="{matchdays.groups.AWAY_TEAM}">
                                            <span>{matchdays.groups.AWAY_TEAM}</span>
                                        </div>
                                    # ENDIF #
                                </div>
                                <div class="game-score away-score width-px-50">{matchdays.groups.AWAY_SCORE}# IF matchdays.groups.C_HAS_PEN # <span class="small">({matchdays.groups.AWAY_PEN})</span># ENDIF #</div>
                            </div>
                        </div>
                    # END matchdays.groups #
                    # START matchdays.brackets #
                        <div id="{matchdays.brackets.MATCH_ID}" class="cell game-match">
                            <div class="game-details small text-italic">
                                <span>{matchdays.brackets.PLAYGROUND}</span>
                                <span>
                                    # IF matchdays.brackets.C_IS_LIVE #
                                        <span class="blink pinned bgc-full notice">{@football.is.live}</span>
                                    # ELSE #
                                        {matchdays.brackets.MATCH_DATE_HOUR_MINUTE}
                                    # ENDIF #
                                </span>
                                <span>{matchdays.brackets.MATCH_ID}</span>
                            </div>
                            <div  class="id-{matchdays.brackets.HOME_ID} game-team game-home# IF matchdays.brackets.C_HOME_FAV # text-strong# ENDIF #"
                                    # IF matchdays.brackets.C_HOME_WIN # style="background-color: {matchdays.brackets.WIN_COLOR}"# ENDIF #>
                                <div class="home-{matchdays.brackets.MATCH_ID} home-team">
                                    # IF matchdays.brackets.HOME_ID #
                                        <div class="flex-team flex-left">
                                            <img src="{PATH_TO_ROOT}/{matchdays.brackets.HOME_LOGO}" alt="{matchdays.brackets.HOME_TEAM}">
                                            <span>{matchdays.brackets.HOME_TEAM}</span>
                                        </div>
                                    # ENDIF #
                                </div>
                                <div class="game-score home-score width-px-50">{matchdays.brackets.HOME_SCORE}# IF matchdays.brackets.C_HAS_PEN # <span class="small">({matchdays.brackets.HOME_PEN})</span># ENDIF #</div>
                            </div>
                            <div class="id-{matchdays.brackets.AWAY_ID} game-team game-away# IF matchdays.brackets.C_AWAY_FAV # text-strong# ENDIF #"
                                    # IF matchdays.brackets.C_AWAY_WIN # style="background-color: {matchdays.brackets.WIN_COLOR}"# ENDIF #>
                                <div class="away-{matchdays.brackets.MATCH_ID} away-team">
                                    # IF matchdays.brackets.AWAY_ID #
                                        <div class="flex-team flex-left">
                                            <img src="{PATH_TO_ROOT}/{matchdays.brackets.AWAY_LOGO}" alt="{matchdays.brackets.AWAY_TEAM}">
                                            <span>{matchdays.brackets.AWAY_TEAM}</span>
                                        </div>
                                    # ENDIF #
                                </div>
                                <div class="game-score away-score width-px-50">{matchdays.brackets.AWAY_SCORE}# IF matchdays.brackets.C_HAS_PEN # <span class="small">({matchdays.brackets.AWAY_PEN})</span># ENDIF #</div>
                            </div>
                        </div>
                    # END matchdays.brackets #
                </div>
            # END matchdays #
        </article>
    # ENDIF #
# ELSE #
    <div class="message-helper bgc notice">{@football.message.no.matches}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>