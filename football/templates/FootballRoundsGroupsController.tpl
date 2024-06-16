# INCLUDE MENU #
<h2># IF C_HAT_DAYS #{@football.day} {DAY}# ELSE #{@football.group} {GROUP}# ENDIF #</h2>
# IF C_HAS_MATCHES #
    # IF C_HAT_DAYS #
        <div class="cell-flex cell-columns-2">
            
            <div class="responsive-table">
                <table class="table bordered-table">
                    <colgroup class="hidden-small-screens">
                        # IF NOT C_ONE_DAY #<col class="width-pc-8" /># ENDIF #
                        <col class="width-pc-4" />
                        <col class="width-pc-40" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-40" />
                    </colgroup>
                    <thead>
                        <tr>
                            # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                            <th aria-label="{@football.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@football.th.team} 1</th>
                            <th colspan="2">{@football.th.score}</th>
                            <th>{@football.th.team} 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START matches #
                            <tr# IF matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                # IF NOT C_ONE_DAY #<td>{matches.MATCH_DATE_SHORT}</td># ENDIF #
                                <td>{matches.MATCH_DATE_HOUR_MINUTE}</td>
                                <td class="align-right# IF matches.C_HOME_FAV # text-strong# ENDIF #">
                                    <div class="flex-team flex-right">
                                        <span>{matches.HOME_TEAM}</span>
                                        <img src="{PATH_TO_ROOT}/{matches.HOME_LOGO}" alt="{matches.HOME_TEAM}">
                                    </div>
                                </td>
                                <td>{matches.HOME_SCORE}</td>
                                <td>{matches.AWAY_SCORE}</td>
                                <td class="align-left# IF matches.C_AWAY_FAV # text-strong# ENDIF #">
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{matches.AWAY_LOGO}" alt="{matches.AWAY_TEAM}">
                                        <span>{matches.AWAY_TEAM}</span>
                                    </div>
                                </td>
                            </tr>
                        # END matches #
                    </tbody>
                </table>
            </div>
            <div class="responsive-table">
                <table class="table bordered-table">
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
                            <th>{@football.th.rank}</th>
                            <th>{@football.th.team}</th>
                            <th>{@football.th.points}</th>
                            <th>{@football.th.played}</th>
                            <th>{@football.th.win}</th>
                            <th>{@football.th.draw}</th>
                            <th>{@football.th.loss}</th>
                            <th>{@football.th.goals.for}</th>
                            <th>{@football.th.goals.against}</th>
                            <th>{@football.th.goal.average}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START ranks #
                            <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <td>{ranks.RANK}</td>
                                <td class="">
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
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
        <div class="cell-flex cell-columns-2">
            <div>
                <table class="table bordered-table">
                    <colgroup class="hidden-small-screens">
                        # IF NOT C_ONE_DAY #<col class="width-pc-8" /># ENDIF #
                        <col class="width-pc-4" />
                        <col class="width-pc-40" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-40" />
                    </colgroup>
                    <thead>
                        <tr>
                            # IF NOT C_ONE_DAY #<th>{@football.th.date}</th># ENDIF #
                            <th aria-label="{@football.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@football.th.team} 1</th>
                            <th colspan="2">{@football.th.score}</th>
                            <th>{@football.th.team} 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START matches #
                            <tr# IF matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                # IF NOT C_ONE_DAY #<td>{matches.MATCH_DATE_SHORT}</td># ENDIF #
                                <td>{matches.MATCH_DATE_HOUR_MINUTE}</td>
                                <td class="align-right# IF matches.C_HOME_FAV # text-strong# ENDIF #">
                                    <div class="flex-team flex-right">
                                        <span>{matches.HOME_TEAM}</span>
                                        <img src="{PATH_TO_ROOT}/{matches.HOME_LOGO}" alt="{matches.HOME_TEAM}">
                                    </div>
                                </td>
                                <td>{matches.HOME_SCORE}</td>
                                <td>{matches.AWAY_SCORE}</td>
                                <td class="align-left# IF matches.C_AWAY_FAV # text-strong# ENDIF #">
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{matches.AWAY_LOGO}" alt="{matches.AWAY_TEAM}">
                                        <span>{matches.AWAY_TEAM}</span>
                                    </div>
                                </td>
                            </tr>
                        # END matches #
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table bordered-table">
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
                            <th>{@football.th.rank}</th>
                            <th>{@football.th.team}</th>
                            <th>{@football.th.points}</th>
                            <th>{@football.th.played}</th>
                            <th>{@football.th.win}</th>
                            <th>{@football.th.draw}</th>
                            <th>{@football.th.loss}</th>
                            <th>{@football.th.goals.for}</th>
                            <th>{@football.th.goals.against}</th>
                            <th>{@football.th.goal.average}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START ranks #
                            <tr class="ranking-color# IF ranks.C_FAV # fav-team# ENDIF #" style="background-color: {ranks.RANK_COLOR}">
                                <td>{ranks.RANK}</td>
                                <td class="">
                                    <div class="flex-team flex-left">
                                        <img src="{PATH_TO_ROOT}/{ranks.TEAM_LOGO}" alt="{ranks.TEAM_NAME}">
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
    # ENDIF #
# ELSE #
    <div class="message-helper bgc notice">{@football.message.no.matches}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>