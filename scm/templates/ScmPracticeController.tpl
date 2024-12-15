<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.games.list}</h2></header>
        <div class="content modal-container">
            # IF C_HAS_GAMES #
                # IF C_RETURN_GAMES #
                    <div class="round-trip-bracket">
                        <div class="cell-flex cell-columns-3">
                            # START games #
                                <div id="{games.GAME_ID}" class="game-container">
                                    <div class="game-details small text-italic">
                                        <span>{games.GAME_ID}</span>
                                        <span>{games.PLAYGROUND}</span>
                                        # IF NOT C_ONE_DAY #
                                            # IF NOT C_FINAL #
                                                <span>{games.GAME_DATE_B_YEAR}</span>
                                            # ENDIF #
                                        # ENDIF #
                                        <span>
                                            # IF C_ONE_DAY #
                                                {games.GAME_DATE_A_HOUR_MINUTE} - {games.GAME_DATE_B_HOUR_MINUTE}
                                            # ELSE #
                                                # IF C_FINAL #
                                                    {games.GAME_DATE_SHORT}
                                                # ELSE #
                                                    {games.GAME_DATE_A_DAY_MONTH} - {games.GAME_DATE_B_DAY_MONTH}
                                                # ENDIF #
                                            # ENDIF #
                                        </span>
                                    </div>
                                    <div class="id-{games.HOME_ID} game-team game-home# IF games.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF games.C_HOME_WIN # style="background-color: {games.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{games.GAME_ID} home-team">
                                            <div class="flex-team flex-left">
                                                # IF games.C_HOME_EMPTY #
                                                    <span>{games.HOME_EMPTY}</span>
                                                # ELSE #
                                                    <img src="{games.HOME_LOGO}" alt="{games.HOME_TEAM}">
                                                    <span><a href="{games.U_HOME_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF games.HOME_FORFEIT # warning# ENDIF #">{games.HOME_TEAM}</a></span>
                                                # ENDIF #
                                            </div>
                                        </div>
                                        <div class="game-team# IF NOT C_HAT_PLAYOFF ## IF NOT C_FINAL # md-width-px-100# ENDIF ## ENDIF #">
                                            <div class="game-score home-score# IF NOT C_HAT_PLAYOFF ## IF NOT C_FINAL # md-width-pc-50# ELSE # md-width-px-50# ENDIF ## ELSE # md-width-px-50# ENDIF #">{games.HOME_SCORE}</div>
                                            # IF NOT C_HAT_PLAYOFF #
                                                # IF NOT C_FINAL #
                                                    <div class="game-score home-score md-width-pc-50">{games.HOME_SCORE_B}# IF games.C_HAS_PEN # <span class="small">({games.HOME_PEN})</span># ENDIF #</div>
                                                # ENDIF #
                                            # ENDIF #
                                        </div>
                                    </div>
                                    <div class="id-{games.AWAY_ID} game-team game-away# IF games.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF games.C_AWAY_WIN # style="background-color: {games.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{games.GAME_ID} away-team">
                                            <div class="flex-team flex-left">
                                                # IF games.C_AWAY_EMPTY #
                                                    <span>{games.AWAY_EMPTY}</span>
                                                # ELSE #
                                                    <img src="{games.AWAY_LOGO}" alt="{games.AWAY_TEAM}">
                                                    <span><a href="{games.U_AWAY_CALENDAR}" aria-label="{@scm.club.see.calendar}" class="offload# IF games.AWAY_FORFEIT # warning# ENDIF #">{games.AWAY_TEAM}</a></span>
                                                # ENDIF #
                                            </div>
                                        </div>
                                        <div class="game-team# IF NOT C_HAT_PLAYOFF ## IF NOT C_FINAL # md-width-px-100# ENDIF ## ENDIF #">
                                            <div class="game-score away-score# IF NOT C_HAT_PLAYOFF ## IF NOT C_FINAL # md-width-pc-50# ELSE # md-width-px-50# ENDIF ## ELSE # md-width-px-50# ENDIF #">{games.AWAY_SCORE}</div>
                                            # IF NOT C_HAT_PLAYOFF #
                                                # IF NOT C_FINAL #
                                                    <div class="game-score away-score md-width-pc-50">{games.AWAY_SCORE_B}# IF games.C_HAS_PEN # <span class="small">({games.AWAY_PEN})</span># ENDIF #</div>
                                                # ENDIF #
                                            # ENDIF #
                                        </div>
                                    </div>
                                </div>
                            # END games #
                        </div>
                    </div>
                # ELSE #
                    # START dates #
                        <h3>{dates.DATE}</h3>
                        <div class="cell-flex cell-columns-3">
                            # START dates.games #
                                <div id="{dates.games.GAME_ID}" class="game-container">
                                    <div class="game-details small text-italic">
                                        <span>{dates.games.PLAYGROUND}</span>
                                        <span># IF C_ONE_DAY #{dates.games.GAME_DATE_HOUR_MINUTE}# ELSE #{dates.games.GAME_DATE_FULL}# ENDIF #</span>
                                        <div>
                                            # IF dates.games.C_HAS_DETAILS #
                                                <a data-modal="" data-target="target-panel-{dates.games.GAME_ID}" aria-label="{@scm.game.event.details}">
                                                    <i class="far fa-file-lines"></i> {dates.games.GAME_ID}
                                                </a>
                                                <div id="target-panel-{dates.games.GAME_ID}" class="modal modal-animation">
                                                    <div class="close-modal" aria-label="{@common.close}"></div>
                                                    <div class="content-panel">
                                                        <div class="align-right"><a href="#" class="error big hide-modal" aria-label="{@common.close}"><i class="far fa-circle-xmark" aria-hidden="true"></i></a></div>
                                                        <div class="cell-flex cell-columns-2 cell-tile">
                                                            <div class="home-team cell">
                                                                <div class="cell-header">
                                                                    <div class="cell-name">
                                                                        <a href="{dates.games.U_HOME_CLUB}" class="offload">{dates.games.HOME_TEAM}</a>
                                                                    </div>
                                                                    # IF dates.games.C_HAS_HOME_LOGO #<img class="smaller md-width-px-25" src="{dates.games.HOME_LOGO}" alt="{dates.games.HOME_TEAM}"># ENDIF #
                                                                </div>
                                                                <div class="cell-score bigger align-center">
                                                                    {dates.games.HOME_SCORE}
                                                                </div>
                                                                # IF dates.games.C_HAS_PEN #
                                                                    <div class="cell-infos">
                                                                        <span class="text-strong">{@scm.game.event.penalties}</span>
                                                                        <span>{dates.games.HOME_PEN}</span>
                                                                    </div>
                                                                # ENDIF #
                                                                <div class="cell-details">{@scm.game.event.goals}</div>
                                                                # START dates.games.home_goals #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.home_goals.PLAYER}</span>
                                                                        <span>{dates.games.home_goals.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.home_goals #
                                                                <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                # START dates.games.home_yellow #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.home_yellow.PLAYER}</span>
                                                                        <span>{dates.games.home_yellow.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.home_yellow #
                                                                <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                # START dates.games.home_red #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.home_red.PLAYER}</span>
                                                                        <span>{dates.games.home_red.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.home_red #
                                                            </div>
                                                            <div class="away-team cell">
                                                                <div class="cell-header">
                                                                    <div class="cell-name">
                                                                        <a href="{dates.games.U_AWAY_CLUB}" class="offload">{dates.games.AWAY_TEAM}</a>
                                                                    </div>
                                                                    # IF dates.games.C_HAS_AWAY_LOGO #<img class="smaller md-width-px-25" src="{dates.games.AWAY_LOGO}" alt="{dates.games.AWAY_TEAM}"># ENDIF #
                                                                </div>
                                                                <div class="cell-score bigger align-center">
                                                                    {dates.games.AWAY_SCORE}
                                                                </div>
                                                                # IF dates.games.C_HAS_PEN #
                                                                    <div class="cell-infos">
                                                                        <span class="text-strong">{@scm.game.event.penalties}</span>
                                                                        <span>{dates.games.AWAY_PEN}</span>
                                                                    </div>
                                                                # ENDIF #
                                                                <div class="cell-details">{@scm.game.event.goals}</div>
                                                                # START dates.games.away_goals #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.away_goals.PLAYER}</span>
                                                                        <span>{dates.games.away_goals.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.away_goals #
                                                                <div class="cell-details">{@scm.game.event.cards.yellow}</div>
                                                                # START dates.games.away_yellow #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.away_yellow.PLAYER}</span>
                                                                        <span>{dates.games.away_yellow.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.away_yellow #
                                                                <div class="cell-details">{@scm.game.event.cards.red}</div>
                                                                # START dates.games.away_red #
                                                                    <div class="cell-infos">
                                                                        <span>{dates.games.away_red.PLAYER}</span>
                                                                        <span>{dates.games.away_red.TIME}'</span>
                                                                    </div>
                                                                # END dates.games.away_red #
                                                            </div>
                                                        </div>
                                                        # IF dates.games.C_VIDEO #
                                                            <a href="{dates.games.U_VIDEO}" class="button d-block align-center" target="blank" rel="noopener noreferer">
                                                                <i class="far fa-circle-play"></i> {@scm.watch.video}
                                                            </a>
                                                        # ENDIF #
                                                        # IF dates.games.SUMMARY #
                                                            {dates.games.SUMMARY}
                                                        # ENDIF #
                                                        # IF dates.games.STADIUM #
                                                            <div class="md-width-pc-50 m-a">{dates.games.STADIUM}</div>
                                                        # ENDIF #
                                                    </div>
                                                </div>
                                            # ELSE #
                                                {dates.games.GAME_ID}
                                            # ENDIF #
                                        </div>
                                    </div>
                                    <div class="id-{dates.games.HOME_ID} game-team game-home# IF dates.games.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF dates.games.C_HOME_WIN # style="background-color: {dates.games.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{dates.games.GAME_ID} home-team">
                                            <div class="flex-team flex-left">
                                                # IF dates.games.C_HOME_EMPTY #
                                                    <span>{dates.games.HOME_EMPTY}</span>
                                                # ELSE #
                                                    # IF dates.games.C_HAS_HOME_LOGO #<img src="{dates.games.HOME_LOGO}" alt="{dates.games.HOME_TEAM}"># ENDIF #
                                                    <span>{dates.games.HOME_TEAM}</span>
                                                # ENDIF #
                                            </div>
                                        </div>
                                        <div class="game-score home-score md-width-px-50 align-center">{dates.games.HOME_SCORE}# IF dates.games.C_HAS_PEN # <span class="small">({dates.games.HOME_PEN})</span># ENDIF #</div>
                                    </div>
                                    <div class="id-{dates.games.AWAY_ID} game-team game-away# IF dates.games.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF dates.games.C_AWAY_WIN # style="background-color: {dates.games.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{dates.games.GAME_ID} away-team">
                                            <div class="flex-team flex-left">
                                                # IF dates.games.C_AWAY_EMPTY #
                                                    <span>{dates.games.AWAY_EMPTY}</span>
                                                # ELSE #
                                                    # IF dates.games.C_HAS_AWAY_LOGO #<img src="{dates.games.AWAY_LOGO}" alt="{dates.games.AWAY_TEAM}"># ENDIF #
                                                    <span>{dates.games.AWAY_TEAM}</span>
                                                # ENDIF #
                                            </div>
                                        </div>
                                        <div class="game-score away-score md-width-px-50 align-center">{dates.games.AWAY_SCORE}# IF dates.games.C_HAS_PEN # <span class="small">({dates.games.AWAY_PEN})</span># ENDIF #</div>
                                    </div>
                                </div>
                            # END dates.games #
                        </div>
                    # END dates #
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