<section id="module-scm" class="single-item modal-container">
    # INCLUDE MENU #
    <article>
        <header><h2><span class="small">{@scm.team.results} :</span> {TEAM_NAME}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="responsive-table">
                    <table class="alternated-table width-pc-70 m-a">
                        <colgroup class="hidden-small-screens">
                            <col class="width-pc-5" />
                            # IF NOT C_ONE_DAY #<col class="width-pc-10" /># ENDIF #
                            <col class="width-pc-5" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="width-pc-8" />
                            <col class="width-pc-8" />
                            <col class="width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="width-pc-5" />
                        </colgroup>
                        <thead>
                            <tr>
                                # IF C_IS_DAY #
                                    <th aria-label="{@scm.th.day}">{@scm.th.day.short}</th>
                                # ELSE #
                                    <th aria-label="{@scm.th.round}">{@scm.th.round.short}</th>
                                # ENDIF #
                                # IF NOT C_ONE_DAY #<th>{@scm.th.date}</th># ENDIF #
                                <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                <th>{@scm.th.home.team}</th>
                                <th colspan="2">{@scm.th.score}</th>
                                <th>{@scm.th.away.team}</th>
                                <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                            </tr>
                        </thead>
                        <tbody>
                            # START games #
                                <tr>
                                    <td># IF C_IS_DAY #{games.DAY}# ELSE #{games.ROUND}# ENDIF #</td>
                                    # IF NOT C_ONE_DAY #<td>{games.GAME_DATE_SHORT}</td># ENDIF #
                                    <td>{games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td>
                                        <div class="flex-team flex-right">
                                            <span# IF games.C_IS_HOME_TEAM # class="{games.TEAM_STATUS}"# ENDIF #># IF games.C_IS_HOME_TEAM #{games.HOME_TEAM}# ELSE #<a href="{games.U_HOME_CALENDAR}" class="offload# IF games.HOME_FORFEIT # warning# ENDIF #">{games.HOME_TEAM}</a># ENDIF #</span>
                                            # IF games.C_HAS_HOME_LOGO #<img src="{games.HOME_LOGO}" alt="{games.HOME_TEAM}"># ENDIF #
                                        </div>
                                    </td>
                                    # IF games.C_STATUS #
                                        <td colspan="2">{games.STATUS}</td>
                                    # ELSE #
                                        <td>{games.HOME_SCORE}</td>
                                        <td>{games.AWAY_SCORE}</td>
                                    # ENDIF #
                                    <td>
                                        <div class="flex-team flex-left">
                                            # IF games.C_HAS_AWAY_LOGO #<img src="{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}"># ENDIF #
                                            <span# IF games.C_IS_AWAY_TEAM # class="{games.TEAM_STATUS}"# ENDIF #># IF games.C_IS_AWAY_TEAM #{games.AWAY_TEAM}# ELSE #<a href="{games.U_AWAY_CALENDAR}" class="offload# IF games.AWAY_FORFEIT # warning# ENDIF #">{games.AWAY_TEAM}</a># ENDIF #</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span data-modal="" data-target="target-panel-{games.GAME_ID}">
                                            # IF games.C_HAS_DETAILS #
                                                # IF games.C_VIDEO #
                                                    <i class="far fa-circle-play"></i>
                                                # ELSE #
                                                    <i class="far fa-file-lines"></i>
                                                # ENDIF #
                                            # ENDIF #
                                        </span>
                                        <div id="target-panel-{games.GAME_ID}" class="modal modal-animation">
                                            <div class="close-modal" aria-label="{@common.close}"></div>
                                            <div class="content-panel">
                                                <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                    <div class="home-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{games.U_HOME_CLUB}" class="offload">{games.HOME_TEAM}</a>
                                                            </div>
                                                            # IF games.C_HAS_HOME_LOGO #<img class="smaller width-px-25" src="{games.HOME_LOGO}" alt="{games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {games.HOME_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                        # START games.home_goals #
                                                            <div class="cell-infos">
                                                                <span>{games.home_goals.PLAYER}</span>
                                                                <span>{games.home_goals.TIME}'</span>
                                                            </div>
                                                        # END games.home_goals #
                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                        # START games.home_yellow #
                                                            <div class="cell-infos">
                                                                <span>{games.home_yellow.PLAYER}</span>
                                                                <span>{games.home_yellow.TIME}'</span>
                                                            </div>
                                                        # END games.home_yellow #
                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                        # START games.home_red #
                                                            <div class="cell-infos">
                                                                <span>{games.home_red.PLAYER}</span>
                                                                <span>{games.home_red.TIME}'</span>
                                                            </div>
                                                        # END games.home_red #
                                                    </div>
                                                    <div class="away-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{games.U_AWAY_CLUB}" class="offload">{games.AWAY_TEAM}</a>
                                                            </div>
                                                            # IF games.C_HAS_AWAY_LOGO #<img class="smaller width-px-25" src="{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {games.AWAY_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                        # START games.away_goals #
                                                            <div class="cell-infos">
                                                                <span>{games.away_goals.PLAYER}</span>
                                                                <span>{games.away_goals.TIME}'</span>
                                                            </div>
                                                        # END games.away_goals #
                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                        # START games.away_yellow #
                                                            <div class="cell-infos">
                                                                <span>{games.away_yellow.PLAYER}</span>
                                                                <span>{games.away_yellow.TIME}'</span>
                                                            </div>
                                                        # END games.away_yellow #
                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                        # START games.away_red #
                                                            <div class="cell-infos">
                                                                <span>{games.away_red.PLAYER}</span>
                                                                <span>{games.away_red.TIME}'</span>
                                                            </div>
                                                        # END games.away_red #
                                                    </div>
                                                </div>
                                                # IF games.C_VIDEO #
                                                    <a href="{games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                    </a>
                                                # ENDIF #
                                                # IF games.SUMMARY #
                                                    {games.SUMMARY}
                                                # ENDIF #
                                                # IF games.STADIUM #
                                                    <div class="width-pc-50 m-a">{games.STADIUM}</div>
                                                # ENDIF #
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            # END games #
                        </tbody>
                    </table>
                </div>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>