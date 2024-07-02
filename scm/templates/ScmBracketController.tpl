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
                                                        # IF rounds.games.HOME_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{rounds.games.HOME_LOGO}" alt="{rounds.games.HOME_TEAM}">
                                                                <span>{rounds.games.HOME_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
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
                                                        # IF rounds.games.AWAY_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{rounds.games.AWAY_LOGO}" alt="{rounds.games.AWAY_TEAM}">
                                                                <span>{rounds.games.AWAY_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
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
                    # IF C_LOOSER_BRACKET #
                        <h3>{@scm.looser.bracket}</h3>
                        <div class="looser-bracket">
                            <div class="cell-bracket">
                                # START l_rounds #
                                    <div# IF l_rounds.C_ALL_PLACES # id="looser-main-round-{l_rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF l_rounds.C_ALL_PLACES # all-places# ENDIF #">
                                        <h5 class="bracket-round-title">{l_rounds.L_TITLE}</h5>
                                        <div class="bracket-round-games">
                                            # START l_rounds.games #
                                                <div id="{l_rounds.games.GAME_ID}" class="game-container">
                                                    <div class="game-details small text-italic">
                                                        <span>{l_rounds.games.PLAYGROUND}</span>
                                                        <span># IF C_ONE_DAY #{l_rounds.games.GAME_DATE_HOUR_MINUTE}# ELSE #{l_rounds.games.GAME_DATE_FULL}# ENDIF #</span>
                                                        <span>{l_rounds.games.GAME_ID}</span>
                                                    </div>
                                                    <div  class="id-{l_rounds.games.HOME_ID} game-team game-home# IF l_rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                            # IF l_rounds.games.C_HOME_WIN # style="background-color: {l_rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="home-{l_rounds.games.GAME_ID} home-team">
                                                            # IF l_rounds.games.HOME_ID #
                                                                <div class="flex-team flex-left">
                                                                    <img src="{PATH_TO_ROOT}/{l_rounds.games.HOME_LOGO}" alt="{l_rounds.games.HOME_TEAM}">
                                                                    <span>{l_rounds.games.HOME_TEAM}</span>
                                                                </div>
                                                            # ENDIF #
                                                        </div>
                                                        <div class="game-score home-score width-px-50">{l_rounds.games.HOME_SCORE}# IF l_rounds.games.C_HAS_PEN # <span class="small">({l_rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                    </div>
                                                    <div class="id-{l_rounds.games.AWAY_ID} game-team game-away# IF l_rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                            # IF l_rounds.games.C_AWAY_WIN # style="background-color: {l_rounds.games.WIN_COLOR}"# ENDIF #>
                                                        <div class="away-{l_rounds.games.GAME_ID} away-team">
                                                            # IF l_rounds.games.AWAY_ID #
                                                                <div class="flex-team flex-left">
                                                                    <img src="{PATH_TO_ROOT}/{l_rounds.games.AWAY_LOGO}" alt="{l_rounds.games.AWAY_TEAM}">
                                                                    <span>{l_rounds.games.AWAY_TEAM}</span>
                                                                </div>
                                                            # ENDIF #
                                                        </div>
                                                        <div class="game-score away-score width-px-50">{l_rounds.games.AWAY_SCORE}# IF l_rounds.games.C_HAS_PEN # <span class="small">({l_rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                    </div>
                                                </div>
                                            # END l_rounds.games #
                                        </div>
                                    </div>
                                # END l_rounds #
                            </div>
                            <div class="cell-bracket">
                                # START l_rounds #
                                    <div id="looser-sub-round-{l_rounds.ROUND_ID}" class="bracket-l_rounds">
                                        <div class="bracket-round-games"></div>
                                    </div>
                                # END l_rounds #
                            </div>
                        </div>
                    # ENDIF #
                    # IF C_LOOSER_BRACKET #<h3>{@scm.winner.bracket}</h3># ENDIF #
                    <div class="winner-bracket">
                        <div class="cell-bracket">
                            # START w_rounds #
                                <div# IF w_rounds.C_ALL_PLACES # id="winner-main-round-{w_rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF w_rounds.C_ALL_PLACES # all-places# ENDIF #">
                                    <h5 class="bracket-round-title">{w_rounds.L_TITLE}</h5>
                                    <div class="bracket-round-games">
                                        # START w_rounds.games #
                                            <div id="{w_rounds.games.GAME_ID}" class="game-container">
                                                <div class="game-details small text-italic">
                                                    <span>{w_rounds.games.PLAYGROUND}</span>
                                                    <span># IF C_ONE_DAY #{w_rounds.games.GAME_DATE_HOUR_MINUTE}# ELSE #{w_rounds.games.GAME_DATE_FULL}# ENDIF #</span>
                                                    <span>{w_rounds.games.GAME_ID}</span>
                                                </div>
                                                <div class="id-{w_rounds.games.HOME_ID} game-team game-home# IF w_rounds.games.C_HOME_FAV # text-strong# ENDIF #"
                                                        # IF w_rounds.games.C_HOME_WIN # style="background-color: {w_rounds.games.WIN_COLOR}"# ENDIF #>
                                                    <div class="home-{w_rounds.games.GAME_ID} home-team">
                                                        # IF w_rounds.games.HOME_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{w_rounds.games.HOME_LOGO}" alt="{w_rounds.games.HOME_TEAM}">
                                                                <span>{w_rounds.games.HOME_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
                                                    </div>
                                                    <div class="game-score home-score width-px-50 align-center">{w_rounds.games.HOME_SCORE}# IF w_rounds.games.C_HAS_PEN # <span class="small">({w_rounds.games.HOME_PEN})</span># ENDIF #</div>
                                                </div>
                                                <div class="id-{w_rounds.games.AWAY_ID} game-team game-away# IF w_rounds.games.C_AWAY_FAV # text-strong# ENDIF #"
                                                        # IF w_rounds.games.C_AWAY_WIN # style="background-color: {w_rounds.games.WIN_COLOR}"# ENDIF #>
                                                    <div class="away-{w_rounds.games.GAME_ID} away-team">
                                                        # IF w_rounds.games.AWAY_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{w_rounds.games.AWAY_LOGO}" alt="{w_rounds.games.AWAY_TEAM}">
                                                                <span>{w_rounds.games.AWAY_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
                                                    </div>
                                                    <div class="game-score away-score width-px-50 align-center">{w_rounds.games.AWAY_SCORE}# IF w_rounds.games.C_HAS_PEN # <span class="small">({w_rounds.games.AWAY_PEN})</span># ENDIF #</div>
                                                </div>
                                            </div>
                                        # END w_rounds.games #
                                    </div>
                                </div>
                            # END w_rounds #
                        </div>
                        # IF C_LOOSER_BRACKET #
                            <div class="cell-bracket">
                                # START w_rounds #
                                    <div id="winner-sub-round-{w_rounds.ROUND_ID}" class="bracket-w_rounds">
                                        <div class="bracket-round-games"></div>
                                    </div>
                                # END w_rounds #
                            </div>
                        # ENDIF #
                    </div>
                # ENDIF #
                # INCLUDE JS_DOC #
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
            let elements = document.querySelectorAll('[id*="' + target + '-main-round-"]');

            elements.forEach(element => {
                let idName = element.getAttribute('id');
                let split = idName.split('-');
                let id = split[split.length - 1];
                let mainRound = document.querySelector('#' + target + '-main-round-' + id + '');
                let subRound = document.querySelector('#' + target + '-sub-round-' + id + '');
                let gameCount = mainRound.querySelectorAll('.game-container').length;

                if (gameCount >= 2) {
                    let lastTwoGames = Array.from(mainRound.querySelectorAll('.game-container')).slice(gameCount - (gameCount / 2), gameCount);
                    lastTwoGames.forEach(game => subRound.appendChild(game));
                }
            });
        }
        move_games('winner');
        move_games('looser');
    </script>
# ENDIF #