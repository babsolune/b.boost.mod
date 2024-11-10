<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.games.brackets.stage}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
                # IF C_FINALS_RANKING #
                    # START groups #
                        <h3>{@scm.group} {groups.GROUP}</h3>
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
                                        <col class="width-pc-5" />
                                    </colgroup>
                                    <thead>
                                        <tr>
                                            <th aria-label="{@scm.th.hourly}"><i class="far fa-clock"></i></th>
                                            <th>{@scm.th.home.team}</th>
                                            <th colspan="2">{@scm.th.score}</th>
                                            <th>{@scm.th.away.team}</th>
                                            # IF C_DISPLAY_PLAYGROUNDS #<th>{@scm.th.playground}</th># ENDIF #
                                            <th aria-label="{@scm.th.details}"><i class="fa fa-align-left"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        # START groups.rounds #
                                            <tr>
                                                <td colspan="# IF C_DISPLAY_PLAYGROUNDS #7# ELSE #6# ENDIF #">{@scm.round} {groups.rounds.ROUND}</td>
                                            </tr>
                                            # START groups.rounds.games #
                                                <tr# IF groups.rounds.games.C_HAS_SCORE # class="has-score-color"# ENDIF #>
                                                    <td>{groups.rounds.games.GAME_DATE_HOUR_MINUTE}</td>
                                                    <td class="align-right# IF groups.rounds.games.C_HOME_FAV # text-strong# ENDIF #">
                                                        <div class="flex-team flex-right">
                                                            <span>{groups.rounds.games.HOME_TEAM}</span>
                                                            # IF groups.rounds.games.C_HAS_HOME_LOGO #<img src="{groups.rounds.games.HOME_LOGO}" alt="{groups.rounds.games.HOME_TEAM}"># ENDIF #
                                                        </div>
                                                    </td>
                                                    # IF groups.rounds.games.C_STATUS #
                                                        <td colspan="2">{groups.rounds.games.STATUS}</td>
                                                    # ELSE #
                                                        <td>{groups.rounds.games.HOME_SCORE}</td>
                                                        <td>{groups.rounds.games.AWAY_SCORE}</td>
                                                    # ENDIF #
                                                    <td class="align-left# IF groups.rounds.games.C_AWAY_FAV # text-strong# ENDIF #">
                                                        <div class="flex-team flex-left">
                                                            # IF groups.rounds.games.C_HAS_AWAY_LOGO #<img src="{groups.rounds.games.AWAY_LOGO}" alt="{groups.rounds.games.AWAY_TEAM}"># ENDIF #
                                                            <span>{groups.rounds.games.AWAY_TEAM}</span>
                                                        </div>
                                                    </td>
                                                    # IF C_DISPLAY_PLAYGROUNDS #<td>{groups.rounds.games.PLAYGROUND}</td># ENDIF #
                                                    <td>
                                                        <span data-modal="" data-target="target-panel-{groups.rounds.games.GAME_ID}">
                                                            # IF groups.rounds.games.C_HAS_SCORE #
                                                                # IF groups.rounds.games.C_VIDEO #
                                                                    <i class="far fa-circle-play"></i>
                                                                # ELSE #
                                                                    <i class="far fa-file-lines"></i>
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </span>
                                                        <div id="target-panel-{groups.rounds.games.GAME_ID}" class="modal modal-animation">
                                                            <div class="close-modal" aria-label="{@common.close}"></div>
                                                            <div class="content-panel">
                                                                <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                                <div class="cell-flex cell-columns-2 cell-tile">
                                                                    <div class="home-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">{groups.rounds.games.HOME_TEAM}</div>
                                                                            # IF groups.rounds.games.C_HAS_HOME_LOGO #<img class="smaller width-px-25" src="{groups.rounds.games.HOME_LOGO}" alt="{groups.rounds.games.HOME_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {groups.rounds.games.HOME_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                                        # START groups.rounds.games.home_goals #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.home_goals.PLAYER}</span>
                                                                                <span>{groups.rounds.games.home_goals.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_goals #
                                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                                        # START groups.rounds.games.home_yellow #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.home_yellow.PLAYER}</span>
                                                                                <span>{groups.rounds.games.home_yellow.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_yellow #
                                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                                        # START groups.rounds.games.home_red #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.home_red.PLAYER}</span>
                                                                                <span>{groups.rounds.games.home_red.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.home_red #
                                                                    </div>
                                                                    <div class="away-team cell">
                                                                        <div class="cell-header">
                                                                            <div class="cell-name">{groups.rounds.games.AWAY_TEAM}</div>
                                                                            # IF groups.rounds.games.C_HAS_AWAY_LOGO #<img class="smaller width-px-25" src="{groups.rounds.games.AWAY_LOGO}" alt="{groups.rounds.games.AWAY_TEAM}"># ENDIF #
                                                                        </div>
                                                                        <div class="cell-score bigger align-center">
                                                                            {groups.rounds.games.AWAY_SCORE}
                                                                        </div>
                                                                        <div class="cell-details">{@scm.event.goals}</div>
                                                                        # START groups.rounds.games.away_goals #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.away_goals.PLAYER}</span>
                                                                                <span>{groups.rounds.games.away_goals.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_goals #
                                                                        <div class="cell-details">{@scm.event.yellow.cards}</div>
                                                                        # START groups.rounds.games.away_yellow #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.away_yellow.PLAYER}</span>
                                                                                <span>{groups.rounds.games.away_yellow.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_yellow #
                                                                        <div class="cell-details">{@scm.event.red.cards}</div>
                                                                        # START groups.rounds.games.away_red #
                                                                            <div class="cell-infos">
                                                                                <span>{groups.rounds.games.away_red.PLAYER}</span>
                                                                                <span>{groups.rounds.games.away_red.TIME}'</span>
                                                                            </div>
                                                                        # END groups.rounds.games.away_red #
                                                                    </div>
                                                                </div>
                                                                # IF groups.rounds.games.C_VIDEO #
                                                                    <a href="{groups.rounds.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                        <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                                    </a>
                                                                # ENDIF #
                                                                # IF groups.rounds.games.SUMMARY #
                                                                    {groups.rounds.games.SUMMARY}
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            # END groups.rounds.games #
                                        # END groups.rounds #
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
                                        # START groups.ranks #
                                            <tr class="ranking-color# IF groups.ranks.C_FAV # fav-team# ENDIF #" style="background-color: {groups.ranks.RANK_COLOR}">
                                                <td>{groups.ranks.RANK}</td>
                                                <td class="">
                                                    <div class="flex-team flex-left">
                                                        <img src="{groups.ranks.TEAM_LOGO}" alt="{groups.ranks.TEAM_NAME}">
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
                    # END groups #
                # ELSE #
                    # IF C_RETURN_GAMES #
                        <div class="round-trip-bracket">
                            <div class="cell-bracket">
                                # START rounds #
                                    <div# IF rounds.C_ALL_PLACES # id="round-trip-main-round-{rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF rounds.C_ALL_PLACES # all-places# ENDIF #">
                                        <h5 class="bracket-round-title">{rounds.L_TITLE}</h5>
                                        <div class="bracket-round-games">
                                            # IF rounds.C_DRAW_GAMES #<div># ENDIF #
                                            # START rounds.games #
                                                <div id="{rounds.games.GAME_ID}" class="game-container">
                                                    <div class="game-details small text-italic">
                                                        <span>{rounds.games.GAME_ID}</span>
                                                        <span>{rounds.games.PLAYGROUND}</span>
                                                        # IF NOT C_ONE_DAY #
                                                            # IF NOT rounds.C_FINAL #
                                                                <span>{rounds.games.GAME_DATE_B_YEAR}</span>
                                                            # ENDIF #
                                                        # ENDIF #
                                                        <span>
                                                            # IF C_ONE_DAY #
                                                                {rounds.games.GAME_DATE_A_HOUR_MINUTE} - {rounds.games.GAME_DATE_B_HOUR_MINUTE}
                                                            # ELSE #
                                                                # IF rounds.C_FINAL #
                                                                    {rounds.games.GAME_DATE_SHORT}
                                                                # ELSE #
                                                                    {rounds.games.GAME_DATE_A_DAY_MONTH} - {rounds.games.GAME_DATE_B_DAY_MONTH}
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </span>
                                                    </div>
                                                    <div  class="id-{rounds.games.HOME_ID} game-team game-home# IF rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                            # IF rounds.games.C_HOME_WIN # style="background-color: {rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="home-{rounds.games.GAME_ID} home-team">
                                                            <div class="flex-team flex-left">
                                                                # IF rounds.games.C_HOME_EMPTY #
                                                                    <span>{rounds.games.HOME_EMPTY}</span>
                                                                # ELSE #
                                                                    <img src="{rounds.games.HOME_LOGO}" alt="{rounds.games.HOME_TEAM}">
                                                                    <span><a href="{rounds.games.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{rounds.games.HOME_TEAM}</a></span>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                        <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-px-100# ENDIF ## ENDIF #">
                                                            <div class="game-score home-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-pc-50# ELSE # width-px-50# ENDIF ## ELSE # width-px-50# ENDIF #">{rounds.games.HOME_SCORE}</div>
                                                            # IF NOT rounds.C_HAT_PLAYOFF #
                                                                # IF NOT rounds.C_FINAL #
                                                                    <div class="game-score home-score width-pc-50">{rounds.games.HOME_SCORE_B}# IF rounds.games.C_HAS_PEN # <span class="small">({rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                    <div class="id-{rounds.games.AWAY_ID} game-team game-away# IF rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                            # IF rounds.games.C_AWAY_WIN # style="background-color: {rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="away-{rounds.games.GAME_ID} away-team">
                                                            <div class="flex-team flex-left">
                                                                # IF rounds.games.C_AWAY_EMPTY #
                                                                    <span>{rounds.games.AWAY_EMPTY}</span>
                                                                # ELSE #
                                                                    <img src="{rounds.games.AWAY_LOGO}" alt="{rounds.games.AWAY_TEAM}">
                                                                    <span><a href="{rounds.games.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{rounds.games.AWAY_TEAM}</a></span>
                                                                # ENDIF #
                                                            </div>
                                                        </div>
                                                        <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-px-100# ENDIF ## ENDIF #">
                                                            <div class="game-score away-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-pc-50# ELSE # width-px-50# ENDIF ## ELSE # width-px-50# ENDIF #">{rounds.games.AWAY_SCORE}</div>
                                                            # IF NOT rounds.C_HAT_PLAYOFF #
                                                                # IF NOT rounds.C_FINAL #
                                                                    <div class="game-score away-score width-pc-50">{rounds.games.AWAY_SCORE_B}# IF rounds.games.C_HAS_PEN # <span class="small">({rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                                # ENDIF #
                                                            # ENDIF #
                                                        </div>
                                                    </div>
                                                </div>
                                            # END rounds.games #
                                            # IF rounds.C_DRAW_GAMES #</div># ENDIF #
                                        </div>
                                    </div>
                                # END rounds #
                            </div>
                        </div>
                    # ELSE #
                        # START brackets #
                            <h3>{brackets.BRACKET_NAME}</h3>
                            <div class="winner-bracket">
                                <div class="cell-bracket">
                                    # START brackets.rounds #
                                        <div# IF brackets.rounds.C_ALL_PLACES # id="bracket-{brackets.BRACKET_ID}-main-round-{brackets.rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF brackets.rounds.C_ALL_PLACES # all-places# ENDIF #">
                                            <h5 class="bracket-round-title">{brackets.rounds.L_TITLE}</h5>
                                            <div class="bracket-round-games">
                                                # START brackets.rounds.games #
                                                    <div id="{brackets.rounds.games.GAME_ID}" class="game-container">
                                                        <div class="game-details small text-italic">
                                                            <span>{brackets.rounds.games.PLAYGROUND}</span>
                                                            <span># IF C_ONE_DAY #{brackets.rounds.games.GAME_DATE_HOUR_MINUTE}# ELSE #{brackets.rounds.games.GAME_DATE_FULL}# ENDIF #</span>
                                                            <span>{brackets.rounds.games.GAME_ID}</span>
                                                        </div>
                                                        <div class="id-{brackets.rounds.games.HOME_ID} game-team game-home# IF brackets.rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                                # IF brackets.rounds.games.C_HOME_WIN # style="background-color: {brackets.rounds.games.WIN_COLOR}"# ENDIF #>
                                                            <div class="home-{brackets.rounds.games.GAME_ID} home-team">
                                                                <div class="flex-team flex-left">
                                                                    # IF brackets.rounds.games.C_HOME_EMPTY #
                                                                        <span>{brackets.rounds.games.HOME_EMPTY}</span>
                                                                    # ELSE #
                                                                        # IF brackets.rounds.games.C_HAS_HOME_LOGO #<img src="{brackets.rounds.games.HOME_LOGO}" alt="{brackets.rounds.games.HOME_TEAM}"># ENDIF #
                                                                        <span><a href="{brackets.rounds.games.U_HOME_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{brackets.rounds.games.HOME_TEAM}</a></span>
                                                                    # ENDIF #
                                                                </div>
                                                            </div>
                                                            <div class="game-score home-score width-px-50 align-center">{brackets.rounds.games.HOME_SCORE}# IF brackets.rounds.games.C_HAS_PEN # <span class="small">({brackets.rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                        </div>
                                                        <div class="id-{brackets.rounds.games.AWAY_ID} game-team game-away# IF brackets.rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                                # IF brackets.rounds.games.C_AWAY_WIN # style="background-color: {brackets.rounds.games.WIN_COLOR}"# ENDIF #>
                                                            <div class="away-{brackets.rounds.games.GAME_ID} away-team">
                                                                <div class="flex-team flex-left">
                                                                    # IF brackets.rounds.games.C_AWAY_EMPTY #
                                                                        <span>{brackets.rounds.games.AWAY_EMPTY}</span>
                                                                    # ELSE #
                                                                        # IF brackets.rounds.games.C_HAS_AWAY_LOGO #<img src="{brackets.rounds.games.AWAY_LOGO}" alt="{brackets.rounds.games.AWAY_TEAM}"># ENDIF #
                                                                        <span><a href="{brackets.rounds.games.U_AWAY_CALENDAR}" aria-label="{@scm.see.club.calendar}" class="offload">{brackets.rounds.games.AWAY_TEAM}</a></span>
                                                                    # ENDIF #
                                                                </div>
                                                            </div>
                                                            <div class="game-score away-score width-px-50 align-center">{brackets.rounds.games.AWAY_SCORE}# IF brackets.rounds.games.C_HAS_PEN # <span class="small">({brackets.rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                        </div>
                                                    </div>
                                                # END brackets.rounds.games #
                                            </div>
                                        </div>
                                    # END brackets.rounds #
                                </div>
                                # IF C_LOOSER_BRACKET #
                                    <div class="cell-bracket">
                                        # START brackets.rounds #
                                            <div id="bracket-{brackets.BRACKET_ID}-sub-round-{brackets.rounds.ROUND_ID}" class="sub-bracket">
                                                <div class="bracket-round-games"></div>
                                            </div>
                                        # END brackets.rounds #
                                    </div>
                                # ENDIF #
                            </div>
                        # END brackets #
                    # ENDIF #
                # ENDIF #
            # ELSE #
                <div class="message-helper bgc notice">{@scm.message.no.games}</div>
            # ENDIF #
        </div>
    </article>
    <footer></footer>
</section>

<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
# IF C_LOOSER_BRACKET #
    <script>
        function move_games(target) {
            let elements = document.querySelectorAll('[id*="bracket-' + target + '-main-round-"]');

            elements.forEach(element => {
                let idName = element.getAttribute('id');
                let split = idName.split('-');
                let id = split[split.length - 1];
                let mainRound = document.querySelector('#bracket-' + target + '-main-round-' + id + '');
                let subRound = document.querySelector('#bracket-' + target + '-sub-round-' + id + '');
                let gameCount = mainRound.querySelectorAll('.game-container').length;

                if (gameCount >= 2) {
                    let lastTwoGames = Array.from(mainRound.querySelectorAll('.game-container')).slice(gameCount - (gameCount / 2), gameCount);
                    lastTwoGames.forEach(game => subRound.appendChild(game));
                }
            });
        }
        # START brackets #
            move_games({brackets.BRACKET_ID});
        # END brackets #
    </script>
# ENDIF #