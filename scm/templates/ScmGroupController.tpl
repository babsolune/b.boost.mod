<section id="module-scm" class="several-items">
	# INCLUDE MENU #
    <h2># IF C_HAT_RANKING #{@scm.day} {DAY}# ELSE #{@scm.group} {GROUP}# ENDIF #</h2>
    # IF C_HAS_GAMES #
        <div class="cell-flex cell-columns-2">
            <div class="responsive-table">
                <table class="bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-4" />
                        <col class="width-pc-40" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-40" />
                        # IF C_DISPLAY_PLAYGROUNDS #<col class="width-pc-10" /># ENDIF #
                    </colgroup>
                    <thead>
                        <tr>
                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@scm.th.team} 1</th>
                            <th colspan="2">{@scm.th.score}</th>
                            <th>{@scm.th.team} 2</th>
                            # IF C_DISPLAY_PLAYGROUNDS #<th>{@scm.th.playground}</th># ENDIF #
                        </tr>
                    </thead>
                    <tbody>
                    # START matchdays #
                        # IF NOT C_HAT_RANKING #
                            <tr>
                                <td colspan="# IF C_DISPLAY_PLAYGROUNDS #6# ELSE #5# ENDIF #"># IF C_ONE_DAY #{@scm.round}# ELSE #{@scm.day}# ENDIF # {matchdays.MATCHDAY}</td>
                            </tr>
                        # ENDIF #
                        # START matchdays.dates #
                            # IF NOT C_ONE_DAY #
                                <tr>
                                    <td colspan="# IF C_DISPLAY_PLAYGROUNDS #6# ELSE #5# ENDIF #">{matchdays.dates.DATE}</td>
                                </tr>
                            # ENDIF #
                            # START matchdays.dates.games #
                                <tr# IF matchdays.dates.games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                    <td>{matchdays.dates.games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="align-right# IF matchdays.dates.games.C_HOME_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span>{matchdays.dates.games.HOME_TEAM}</span>
                                            <img src="{matchdays.dates.games.HOME_LOGO}" alt="{matchdays.dates.games.HOME_TEAM}">
                                        </div>
                                    </td>
                                    <td>{matchdays.dates.games.HOME_SCORE}</td>
                                    <td>{matchdays.dates.games.AWAY_SCORE}</td>
                                    <td class="align-left# IF matchdays.dates.games.C_AWAY_FAV # text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            <img src="{matchdays.dates.games.AWAY_LOGO}" alt="{matchdays.dates.games.AWAY_TEAM}">
                                            <span>{matchdays.dates.games.AWAY_TEAM}</span>
                                        </div>
                                    </td>
                                    # IF C_DISPLAY_PLAYGROUNDS #<td>{matchdays.dates.games.PLAYGROUND}</td># ENDIF #
                                </tr>
                            # END matchdays.dates.games #
                        # END matchdays.dates #
                    # END matchdays #
                    </tbody>
                </table>
            </div>
            <div class="responsive-table">
                <table class="bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-05" />
                        <col class="width-pc-60" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                        <col class="width-pc-05" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th>{@scm.th.rank.short}</th>
                            <th>{@scm.th.team}</th>
                            <th>{@scm.th.points.short}</th>
                            <th>{@scm.th.played.short}</th>
                            <th>{@scm.th.win.short}</th>
                            <th>{@scm.th.draw.short}</th>
                            <th>{@scm.th.loss.short}</th>
                            <th>{@scm.th.goals.for.short}</th>
                            <th>{@scm.th.goals.against.short}</th>
                            <th>{@scm.th.goal.average.short}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START ranks #
                            <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <td>{ranks.RANK}</td>
                                <td class="">
                                    <div class="flex-team flex-left">
                                        <img src="{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
                                        <span>{ranks.TEAM_NAME}</span>
                                    </div>
                                </td>
                                <td>{ranks.POINTS}</td>
                                <td>{ranks.PLAYED}</td>
                                <td>{ranks.WIN}</td>
                                <td>{ranks.DRAW}</td>
                                <td>{ranks.LOSS}</td>
                                <td>{ranks.GOALS_FOR}</td>
                                <td>{ranks.GOALS_AGAINST}</td>
                                <td>{ranks.GOAL_AVERAGE}</td>
                            </tr>
                        # END ranks #
                    </tbody>
                </table>
            </div>
        </div>
    # ELSE #
        <div class="message-helper bgc notice">{@scm.message.no.games}</div>
    # ENDIF #
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>