<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article>
        <header><h2>{@scm.games.brackets.stage}</h2></header>
        <div class="content">
            # IF C_HAS_GAMES #
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
                                                                <span>{rounds.games.HOME_TEAM}</span>
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
                                                                <span>{rounds.games.AWAY_TEAM}</span>
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
                                                                    <img src="{brackets.rounds.games.HOME_LOGO}" alt="{brackets.rounds.games.HOME_TEAM}">
                                                                    <span>{brackets.rounds.games.HOME_TEAM}</span>
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
                                                                    <img src="{brackets.rounds.games.AWAY_LOGO}" alt="{brackets.rounds.games.AWAY_TEAM}">
                                                                    <span>{brackets.rounds.games.AWAY_TEAM}</span>
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