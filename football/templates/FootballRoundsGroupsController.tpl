# INCLUDE MENU #
<h2>{@football.matches.groups.stage}</h2>
# IF C_HAS_MATCHES #
    # IF C_HAT_DAYS #
        <div class="cell-flex cell-columns-2">
            <div class="tabs-container">
                <nav id="days" class="tabs-nav">
                    <ul>
                        # START days #
                            <li><a# IF days.C_FIRST_DAY # class="active-tab"# ENDIF # href="#" data-tabs="" data-target="day-{days.DAY}">{@football.day} {days.DAY}</a></li>
                        # END days #
                    </ul>
                </nav>
                # START days #
                    <div id="day-{days.DAY}" class="tabs tabs-animation# IF days.C_FIRST_DAY # first-tab active-panel# ENDIF #">
                        <div class="content-panel">
                            <table class="table bordered-table">
                                <colgroup class="hidden-small-screens">
                                    <col class="width-pc-4" />
                                    <col class="width-pc-40" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-8" />
                                    <col class="width-pc-40" />
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
                                    # START days.matches #
                                        <tr# IF days.matches.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                            <td># IF C_ONE_DAY #{days.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{days.matches.MATCH_DATE_FULL}# ENDIF #</td>
                                            <td class="align-right# IF days.matches.C_HOME_FAV # text-strong# ENDIF #">
                                                <div class="flex-team flex-right">
                                                    <span>{days.matches.HOME_TEAM}</span>
                                                    <img src="{PATH_TO_ROOT}/{days.matches.HOME_LOGO}" alt="{days.matches.HOME_TEAM}">
                                                </div>
                                            </td>
                                            <td>{days.matches.HOME_SCORE}</td>
                                            <td>{days.matches.AWAY_SCORE}</td>
                                            <td class="align-left# IF days.matches.C_AWAY_FAV # text-strong# ENDIF #">
                                                <div class="flex-team flex-left">
                                                    <img src="{PATH_TO_ROOT}/{days.matches.AWAY_LOGO}" alt="{days.matches.AWAY_TEAM}">
                                                    <span>{days.matches.AWAY_TEAM}</span>
                                                </div>
                                            </td>
                                        </tr>
                                    # END days.matches #
                                </tbody>
                            </table>
                        </div>
                    </div>
                # END days #
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
    # ELSE #
        <div class="tabs-container">
            <nav id="groups" class="tabs-nav">
                <ul>
                    # START groups #
                        <li><a# IF groups.C_FIRST_GROUP # class="active-tab"# ENDIF # href="#" data-tabs="" data-target="group-{groups.GROUP}">{@football.group} {groups.GROUP}</a></li>
                    # END groups #
                </ul>
            </nav>
            # START groups #
                <div id="group-{groups.GROUP}" class="tabs tabs-animation# IF groups.C_FIRST_GROUP # first-tab active-panel# ENDIF #">
                    <div class="content-panel">
                        <div class="cell-flex cell-columns-2">
                            <div>
                                <table class="table bordered-table">
                                    <colgroup class="hidden-small-screens">
                                        <col class="width-pc-4" />
                                        <col class="width-pc-40" />
                                        <col class="width-pc-8" />
                                        <col class="width-pc-8" />
                                        <col class="width-pc-40" />
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
                                                <td class="align-right# IF groups.matches.C_HOME_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-right">
                                                        <span>{groups.matches.HOME_TEAM}</span>
                                                        <img src="{PATH_TO_ROOT}/{groups.matches.HOME_LOGO}" alt="{groups.matches.HOME_TEAM}">
                                                    </div>
                                                </td>
                                                <td>{groups.matches.HOME_SCORE}</td>
                                                <td>{groups.matches.AWAY_SCORE}</td>
                                                <td class="align-left# IF groups.matches.C_AWAY_FAV # text-strong# ENDIF #">
                                                    <div class="flex-team flex-left">
                                                        <img src="{PATH_TO_ROOT}/{groups.matches.AWAY_LOGO}" alt="{groups.matches.AWAY_TEAM}">
                                                        <span>{groups.matches.AWAY_TEAM}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        # END groups.matches #
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
                                        # START groups.ranks #
                                            <tr class="ranking-color# IF groups.ranks.C_FAV # fav-team# ENDIF #" style="background-color: {groups.ranks.RANK_COLOR}">
                                                <td>{groups.ranks.RANK}</td>
                                                <td class="">
                                                    <div class="flex-team flex-left">
                                                        <img src="{PATH_TO_ROOT}/{groups.ranks.TEAM_LOGO}" alt="{groups.ranks.TEAM_NAME}">
                                                        <span>{groups.ranks.TEAM_NAME}</span>
                                                    </div>
                                                </td>
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
                    </div>
                </div>
            # END groups #
        </div>
    # ENDIF #
# ELSE #
    yenapadmatch
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>