<section id="module-scm" class="several-items modal-container">
	# INCLUDE MENU #
    <h2># IF C_HAT_RANKING #{@scm.day} {DAY}# ELSE #{@scm.group} {GROUP}# ENDIF #</h2>
    # IF C_HAS_GAMES #
        <div class="cell-flex cell-columns-2">
            <div class="games-list">
                # IF C_HAT_RANKING #
                    # INCLUDE MATCHDAY_GAMES #
                # ELSE #
                    # INCLUDE ROUND_GAMES #
                # ENDIF #
            </div>
            <div class="responsive-table ranking-list">
                <table class="bordered-table">
                    <colgroup class="hidden-small-screens">
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-60" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
                        <col class="md-width-pc-05" />
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
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>