<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.days.results} {@scm.day} {DAY}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <table class="width-pc-70 bordered-table m-a">
                    <colgroup class="hidden-small-screens">
                        <col class="width-pc-4" />
                        <col class="width-pc-40" />
                        <col class="width-pc-8" />
                        <col class="width-pc-8" />
                        <col class="width-pc-40" />
                    </colgroup>
                    <thead>
                        <tr>
                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                            <th>{@scm.th.home.team}</th>
                            <th colspan="2">{@scm.th.score}</th>
                            <th>{@scm.th.away.team}</th>
                        </tr>
                    </thead>
                    <tbody>
                        # START dates #
                            # IF NOT C_ONE_DAY #
                                <tr>
                                    <td colspan="5">{dates.DATE}</td>
                                </tr>
                            # ENDIF #
                            # START dates.games #
                                <tr# IF dates.games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                    <td>{dates.games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="# IF dates.games.C_HOME_FAV #text-strong# ENDIF #">
                                        <div class="flex-team flex-right">
                                            <span><a href="{dates.games.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{dates.games.HOME_TEAM}</a></span>
                                            <img src="{dates.games.HOME_LOGO}" alt="{dates.games.HOME_TEAM}">
                                        </div>
                                    </td>
                                    # IF dates.games.C_STATUS #
                                        <td colspan="2">{dates.games.STATUS}</td>
                                    # ELSE #
                                        <td>{dates.games.HOME_SCORE}</td>
                                        <td>{dates.games.AWAY_SCORE}</td>
                                    # ENDIF #
                                    <td class="# IF dates.games.C_AWAY_FAV #text-strong# ENDIF #">
                                        <div class="flex-team flex-left">
                                            <img src="{dates.games.AWAY_LOGO}" alt="{dates.games.AWAY_TEAM}">
                                            <span><a href="{dates.games.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{dates.games.AWAY_TEAM}</a></span>
                                        </div>
                                    </td>
                                </tr>
                            # END dates.games #
                        # END dates #
                    </tbody>
                </table>
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>