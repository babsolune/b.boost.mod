# IF C_HAS_GAMES #
    <article class="groups">
        <header class="article-header"><h3>{@scm.groups}</h3></header>
        <div class="content">
            <div class="responsive-table">
                <table class="bordered-table width-auto m-a">
                    <thead>
                        <tr>
                            # START team_groups #
                                <th class="bgc-sub"><a href="{team_groups.U_GROUP}" class="offload">{@scm.group} {team_groups.GROUP}</a></th>
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
            <!--<div class="cell-flex cell-inline flex-between">
                # START team_groups #
                    <div class="cell">
                        <header class="cell-header bgc-sub">
                            <h6 class="cell-name"><a href="{team_groups.U_GROUP}" class="offload">{@scm.group} {team_groups.GROUP}</a></h6>
                        </header>
                        <div class="cell-content">
                            # START team_groups.teams #
                                <div class="flex-team">
                                    <img src="{PATH_TO_ROOT}/{team_groups.teams.TEAM_LOGO}" alt="{team_groups.teams.TEAM_NAME}">
                                    <span>{team_groups.teams.TEAM_NAME}</span>
                                </div>
                            # END team_groups.teams #
                        </div>
                    </div>
                # END team_groups #
            </div>-->
        </div>
    </article>
    # IF C_ONE_DAY #
        <article class="games">
            <header class="article-header flex-between">
                <h3>{@scm.calendar}</h3>
                <button id="next-game" class="button default"><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
            </header>
            <p>{ONE_DAY_DATE}</p>
            <p><h4>{@scm.games.groups.stage}</h4></p>
            <div class="cell-flex cell-columns-4">
                # START groups #
                    <div id="{groups.GAME_ID}" class="cell game-container" data-scroll="{groups.GAME_DATE_TIMESTAMP}">
                        <div class="game-details small text-italic">
                            <span>{groups.PLAYGROUND}</span>
                            <span>
                                # IF groups.C_IS_LIVE #
                                    <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                                # ELSE #
                                    {groups.GAME_DATE_HOUR_MINUTE}
                                # ENDIF #
                            </span>
                            <a href="{groups.U_GROUP}" class="offload">{@scm.group} {groups.GROUP_NAME}</a>
                        </div>
                        <div  class="id-{groups.HOME_ID} game-team game-home# IF groups.C_HOME_FAV # text-strong# ENDIF #"
                                # IF groups.C_HOME_WIN # style="background-color: {groups.WIN_COLOR}"# ENDIF #>
                            <div class="home-{groups.GAME_ID} home-team">
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
                            <div class="away-{groups.GAME_ID} away-team">
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
            <p><h4>{@scm.games.brackets.stage}</h4></p>
            <div class="cell-flex cell-columns-4">
                # START brackets #
                    <div id="{brackets.GAME_ID}" class="cell game-container" data-scroll="{brackets.GAME_DATE_TIMESTAMP}">
                        <div class="game-details small text-italic">
                            <span>{brackets.PLAYGROUND}</span>
                            <span>
                                # IF brackets.C_IS_LIVE #
                                    <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                                # ELSE #
                                    {brackets.GAME_DATE_HOUR_MINUTE}
                                # ENDIF #
                            </span>
                            <span>{brackets.GAME_ID}</span>
                        </div>
                        <div  class="id-{brackets.HOME_ID} game-team game-home# IF brackets.C_HOME_FAV # text-strong# ENDIF #"
                                # IF brackets.C_HOME_WIN # style="background-color: {brackets.WIN_COLOR}"# ENDIF #>
                            <div class="home-{brackets.GAME_ID} home-team">
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
                            <div class="away-{brackets.GAME_ID} away-team">
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
        <article class="games">
            <header class="article-header flex-between">
                <h3>{@scm.calendar}</h3>
                <button id="next-game" class="button default" data-date><i class="fa fa-circle-arrow-down"></i> {@scm.next.games}</button>
            </header>
            # START matchdays #
                <p>{matchdays.DATE}</p>
                <div class="cell-flex cell-columns-4">
                    # START matchdays.groups #
                        <div id="{matchdays.groups.GAME_ID}" class="cell game-container" data-scroll="{matchdays.groups.GAME_DATE_TIMESTAMP}">
                            <div class="game-details small text-italic">
                                <span>{matchdays.groups.PLAYGROUND}</span>
                                <span>
                                    # IF matchdays.groups.C_IS_LIVE #
                                        <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                                    # ELSE #
                                        {matchdays.groups.GAME_DATE_HOUR_MINUTE}
                                    # ENDIF #
                                </span>
                                <a href="{matchdays.groups.U_GROUP}" class="offload">
                                    # IF C_HAT_RANKING #
                                        {@scm.day} {matchdays.groups.DAY_NAME}
                                    # ELSE #
                                        {@scm.group} {matchdays.groups.GROUP_NAME}
                                    # ENDIF #
                                </a>
                            </div>
                            <div  class="id-{matchdays.groups.HOME_ID} game-team game-home# IF matchdays.groups.C_HOME_FAV # text-strong# ENDIF #"
                                    # IF matchdays.groups.C_HOME_WIN # style="background-color: {matchdays.groups.WIN_COLOR}"# ENDIF #>
                                <div class="home-{matchdays.groups.GAME_ID} home-team">
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
                                <div class="away-{matchdays.groups.GAME_ID} away-team">
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
                        <div id="{matchdays.brackets.GAME_ID}" class="cell game-container" data-scroll="{matchdays.brackets.GAME_DATE_TIMESTAMP}">
                            <div class="game-details small text-italic">
                                <span>{matchdays.brackets.PLAYGROUND}</span>
                                <span>
                                    # IF matchdays.brackets.C_IS_LIVE #
                                        <span class="blink pinned bgc-full notice">{@scm.is.live}</span>
                                    # ELSE #
                                        {matchdays.brackets.GAME_DATE_HOUR_MINUTE}
                                    # ENDIF #
                                </span>
                                <span>{matchdays.brackets.GAME_ID}</span>
                            </div>
                            <div  class="id-{matchdays.brackets.HOME_ID} game-team game-home# IF matchdays.brackets.C_HOME_FAV # text-strong# ENDIF #"
                                    # IF matchdays.brackets.C_HOME_WIN # style="background-color: {matchdays.brackets.WIN_COLOR}"# ENDIF #>
                                <div class="home-{matchdays.brackets.GAME_ID} home-team">
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
                                <div class="away-{matchdays.brackets.GAME_ID} away-team">
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
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>