<section id="module-scm" class="single-item modal-container">
    # INCLUDE MENU #
    # IF C_CHARTS #
        <article class="cell-flex cell-columns-3">
            <div class="cell-1-3">
                <canvas class="" id="games-chart"></canvas>
                <script>
                    let ctx_games = document.getElementById("games-chart").getContext('2d');
                    let data_games = {
                        labels: [ ${escapejs(@scm.th.win)}, ${escapejs(@scm.th.draw)}, ${escapejs(@scm.th.loss)} ],
                        datasets: [{
                            data: [
                                # START charts # "{charts.WIN}" # END charts #,
                                # START charts # "{charts.DRAW}" # END charts #,
                                # START charts # "{charts.LOSS}" # END charts #
                            ],
                            backgroundColor: ['#2dcc70', '#9a57b4', '#e94c3d'],
                        }]
                    };
                    let gameChart = new Chart(ctx_games, {
                        type: 'doughnut',
                        data: data_games,
                        options: {
                            responsive: true,
                            aspectRatio : 1,
                            plugins: {
                                legend: {
                                    position: 'top',
                                }
                            }
                        },
                    });
                </script>
            </div>
            <div class="cell-2-3">
                <canvas class="" id="ranks-chart"></canvas>
                <script>
                    let ctx_ranks = document.getElementById("ranks-chart").getContext('2d');
                    let data_ranks = {
                        labels: [ # START ranks #"{ranks.DAY}",# END ranks # ],
                        datasets: [{
                            label : ${escapejs(@scm.ranking)},
                            labels: [ # START ranks # "{@scm.day} {ranks.DAY}",# END ranks # ],
                            data: [ # START ranks # # IF ranks.C_HAS_RANK #"{ranks.RANK}",# ENDIF # # END ranks # ],
                        }]
                    };
                    const tooltip_rank = {
                        yAlign: 'bottom',
                        xAlign: 'center',
                        callbacks: {
                            title: function(context) {
                                return ''
                            },
                            label: function(context) {
                                return context.dataset.labels[context.dataIndex] + ' : ' + context.dataset.data[context.dataIndex]
                            }
                        }
                    };
                    const rankChart = new Chart(ctx_ranks, {
                        type: 'line',
                        data: data_ranks,
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: tooltip_rank,
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        stepSize: 5
                                    }
                                },
                                y: {
                                    min: 1,
                                    max: {TEAMS_NUMBER},
                                    reverse: true,
                                    ticks: {
                                        stepSize: 4
                                    },
                                }
                            }
                        },
                    });
                </script>
            </div>
        </article>
    # ENDIF #
    <article>
        <header><h2><span class="small">{@scm.team.results} :</span> {TEAM_NAME}# IF C_GENERAL_FORFEIT # <span class="warning small">{@scm.params.status.forfeit}</span># ENDIF #</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                <div class="responsive-table">
                    <table class="">
                        <colgroup class="hidden-small-screens">
                            <col class="md-width-pc-5" />
                            # IF NOT C_ONE_DAY #<col class="md-width-pc-10" /># ENDIF #
                            <col class="md-width-pc-5" />
                            <col class="md-width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="md-width-pc-8" />
                            <col class="md-width-pc-8" />
                            <col class="md-width-pc-# IF C_ONE_DAY #37# ELSE #32# ENDIF #" />
                            <col class="md-width-pc-5" />
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
                                <tr class="# IF games.C_IS_AWAY_TEAM #bgc {games.TEAM_STATUS}# ENDIF ## IF games.C_IS_HOME_TEAM #bgc {games.TEAM_STATUS}# ENDIF ## IF games.C_EXEMPT #bgc notice# ENDIF #">
                                    <td># IF C_IS_DAY #{games.DAY}# ELSE #{games.ROUND}# ENDIF #</td>
                                    # IF NOT C_ONE_DAY #<td>{games.GAME_DATE_SHORT}</td># ENDIF #
                                    <td>{games.GAME_DATE_HOUR_MINUTE}</td>
                                    <td class="">
                                        <div class="flex-team flex-right">
                                            <span class="# IF games.HOME_FORFEIT # warning# ENDIF ## IF games.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                                # IF games.C_IS_HOME_TEAM #
                                                    {games.HOME_TEAM}
                                                # ELSE #
                                                    <a aria-label="{@scm.club.see.calendar}" href="{games.U_HOME_CALENDAR}" class="offload# IF games.HOME_FORFEIT # warning# ENDIF ## IF games.HOME_GENERAL_FORFEIT # text-strike warning# ENDIF #">{games.HOME_TEAM}</a>
                                                # ENDIF #
                                            </span>
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
                                            <span class="# IF games.AWAY_FORFEIT # warning# ENDIF ## IF games.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">
                                                # IF games.C_IS_AWAY_TEAM #
                                                    {games.AWAY_TEAM}
                                                # ELSE #
                                                    <a aria-label="{@scm.club.see.calendar}" href="{games.U_AWAY_CALENDAR}" class="offload# IF games.AWAY_FORFEIT # warning# ENDIF ## IF games.AWAY_GENERAL_FORFEIT # text-strike warning# ENDIF #">{games.AWAY_TEAM}</a>
                                                # ENDIF #
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="modal-button --target-panel-{games.GAME_ID}">
                                            # IF games.C_HAS_DETAILS #
                                                # IF games.C_VIDEO #
                                                    <i class="far fa-circle-play"></i>
                                                # ELSE #
                                                    <i class="far fa-file-lines"></i>
                                                # ENDIF #
                                            # ENDIF #
                                        </span>
                                        <div id="target-panel-{games.GAME_ID}" class="modal">
                                            <div class="modal-overlay close-modal" aria-label="{@common.close}"></div>
                                            <div class="modal-content">
                                                <span class="error big hide-modal close-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></span>
                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                    <div class="home-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{games.U_HOME_CLUB}" class="offload">{games.HOME_TEAM}</a>
                                                            </div>
                                                            # IF games.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{games.HOME_LOGO}" alt="{games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {games.HOME_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                        # START games.home_goals #
                                                            <div>
                                                                <span>{games.home_goals.TIME}'</span>
                                                                <span>- {games.home_goals.PLAYER}</span>
                                                            </div>
                                                        # END games.home_goals #
                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                        # START games.home_yellow #
                                                            <div>
                                                                <span>{games.home_yellow.TIME}'</span>
                                                                <span>- {games.home_yellow.PLAYER}</span>
                                                            </div>
                                                        # END games.home_yellow #
                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                        # START games.home_red #
                                                            <div>
                                                                <span>{games.home_red.TIME}'</span>
                                                                <span>- {games.home_red.PLAYER}</span>
                                                            </div>
                                                        # END games.home_red #
                                                    </div>
                                                    <div class="away-team cell">
                                                        <div class="cell-header">
                                                            <div class="cell-name">
                                                                <a href="{games.U_AWAY_CLUB}" class="offload">{games.AWAY_TEAM}</a>
                                                            </div>
                                                            # IF games.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}"># ENDIF #
                                                        </div>
                                                        <div class="cell-score bigger align-center">
                                                            {games.AWAY_SCORE}
                                                        </div>
                                                        <div class="cell-details">{@scm.game.event.goals}</div>
                                                        # START games.away_goals #
                                                            <div>
                                                                <span>{games.away_goals.TIME}'</span>
                                                                <span>- {games.away_goals.PLAYER}</span>
                                                            </div>
                                                        # END games.away_goals #
                                                        <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                        # START games.away_yellow #
                                                            <div>
                                                                <span>{games.away_yellow.TIME}'</span>
                                                                <span>- {games.away_yellow.PLAYER}</span>
                                                            </div>
                                                        # END games.away_yellow #
                                                        <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                        # START games.away_red #
                                                            <div>
                                                                <span>{games.away_red.TIME}'</span>
                                                                <span>- {games.away_red.PLAYER}</span>
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
                                                    <div class="md-width-pc-50 m-a">{games.STADIUM}</div>
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

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight.js"></script>