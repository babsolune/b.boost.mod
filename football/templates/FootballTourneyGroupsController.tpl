# INCLUDE MENU #
# IF C_MATCHES #
    # START groups #
        <h2>{@football.group} {groups.GROUP}</h2>
        <div class="cell-flex cell-columns-2">
            <div>
                <table class="table bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-4" />
                        <col class="width-40" />
                        <col class="width-8" />
                        <col class="width-8" />
                        <col class="width-40" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th># IF C_ONE_DAY #{@football.th.hourly}# ELSE #{@football.th.date}# ENDIF #</th>
                            <th>{@football.th.team} 1</th>
                            <th colspan="2">{@football.th.score}</th>
                            <th>{@football.th.team} 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START groups.matches #
                            <tr# IF groups.matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                <td># IF C_ONE_DAY #{groups.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{groups.matches.MATCH_DATE_FULL}# ENDIF #</td>
                                <td class="align-right">{groups.matches.HOME_TEAM}</td>
                                <td>{groups.matches.HOME_SCORE}</td>
                                <td>{groups.matches.AWAY_SCORE}</td>
                                <td class="align-left">{groups.matches.AWAY_TEAM}</td>
                            </tr>
                        # END groups.matches #
                    </tbody>
                </table>
            </div>
            <div>
                <table class="table bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="width-05" />
                        <col class="width-60" />
                        <col class="width-05" />
                        <col class="width-05" />
                        <col class="width-05" />
                        <col class="width-05" />
                        <col class="width-05" />
                        <col class="width-05" />
                        <col class="width-05" />
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
                        # START groups.ranks #
                            <tr class="ranking-color" style="background-color: # IF groups.ranks.C_PROM #{groups.ranks.PROM_COLOR}# ELSE ## IF groups.ranks.C_RELEG #{groups.ranks.RELEG_COLOR}# ELSE #rgba(0, 0, 0, 0)# ENDIF ## ENDIF #">
                                <td>{groups.ranks.RANK}</td>
                                <td class="align-left">{groups.ranks.TEAM_NAME}</td>
                                <td>{groups.ranks.POINTS}</td>
                                <td>{groups.ranks.PLAYED}</td>
                                <td>{groups.ranks.WIN}</td>
                                <td>{groups.ranks.DRAW}</td>
                                <td>{groups.ranks.LOSS}</td>
                                <td>{groups.ranks.GOALS_FOR}</td>
                                <td>{groups.ranks.GOALS_AGAINST}</td>
                                <td>{groups.ranks.GOAL_AVERAGE}</td>
                            </tr>
                        # END groups.ranks #
                    </tbody>
                </table>
            </div>
        </div>
    # END groups #
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.table# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>