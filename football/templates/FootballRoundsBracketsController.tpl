# INCLUDE MENU #
<article>
    <header><h2>{@football.matches.brackets.stage}</h2></header>
    <div class="content">
        # IF C_HAS_MATCHES #
            # IF C_RETURN_MATCHES #
                <div class="round-trip-bracket">
                    <div class="cell-bracket">
                        # START rounds #
                            <div# IF rounds.C_ALL_PLACES # id="round-trip-main-round-{rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF rounds.C_ALL_PLACES # all-places# ENDIF #">
                                <h5 class="bracket-round-title">{rounds.L_TITLE}</h5>
                                <div class="bracket-round-matches">
                                    # START rounds.matches #
                                        <div id="{rounds.matches.MATCH_ID}" class="game-match">
                                            <div class="game-details small text-italic">
                                                <span>{rounds.matches.MATCH_ID}</span>
                                                <span>{rounds.matches.PLAYGROUND}</span>
                                                # IF NOT C_ONE_DAY #
                                                    # IF NOT rounds.C_FINAL #
                                                        <span>{rounds.matches.MATCH_DATE_B_YEAR}</span>
                                                    # ENDIF #
                                                # ENDIF #
                                                <span>
                                                    # IF C_ONE_DAY #
                                                        {rounds.matches.MATCH_DATE_A_HOUR_MINUTE} - {rounds.matches.MATCH_DATE_B_HOUR_MINUTE}
                                                    # ELSE #
                                                        # IF rounds.C_FINAL #
                                                            {rounds.matches.MATCH_DATE_SHORT}
                                                        # ELSE #
                                                            {rounds.matches.MATCH_DATE_A_DAY_MONTH} - {rounds.matches.MATCH_DATE_B_DAY_MONTH}
                                                        # ENDIF #
                                                    # ENDIF #
                                                </span>
                                            </div>
                                            <div  class="id-{rounds.matches.HOME_ID} game-team game-home# IF rounds.matches.C_HOME_FAV # text-strong# ENDIF #"
                                                    # IF rounds.matches.C_HOME_WIN # style="background-color: {rounds.matches.WIN_COLOR}"# ENDIF #>
                                                <div class="home-{rounds.matches.MATCH_ID} home-team">
                                                    # IF rounds.matches.HOME_ID #
                                                        <div class="flex-team flex-left">
                                                            <img src="{PATH_TO_ROOT}/{rounds.matches.HOME_LOGO}" alt="{rounds.matches.HOME_TEAM}">
                                                            <span>{rounds.matches.HOME_TEAM}</span>
                                                        </div>
                                                    # ENDIF #
                                                </div>
                                                <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-px-100# ENDIF ## ENDIF #">
                                                    <div class="game-score home-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-pc-50# ELSE # width-px-50# ENDIF ## ELSE # width-px-50# ENDIF #">{rounds.matches.HOME_SCORE}</div>
                                                    # IF NOT rounds.C_HAT_PLAYOFF #
                                                        # IF NOT rounds.C_FINAL #
                                                            <div class="game-score home-score width-pc-50">{rounds.matches.HOME_SCORE_B}# IF rounds.matches.C_HAS_PEN # <span class="small">({rounds.matches.HOME_PEN})</span># ENDIF #</div>
                                                        # ENDIF #
                                                    # ENDIF #
                                                </div>
                                            </div>
                                            <div class="id-{rounds.matches.AWAY_ID} game-team game-away# IF rounds.matches.C_AWAY_FAV # text-strong# ENDIF #"
                                                    # IF rounds.matches.C_AWAY_WIN # style="background-color: {rounds.matches.WIN_COLOR}"# ENDIF #>
                                                <div class="away-{rounds.matches.MATCH_ID} away-team">
                                                    # IF rounds.matches.AWAY_ID #
                                                        <div class="flex-team flex-left">
                                                            <img src="{PATH_TO_ROOT}/{rounds.matches.AWAY_LOGO}" alt="{rounds.matches.AWAY_TEAM}">
                                                            <span>{rounds.matches.AWAY_TEAM}</span>
                                                        </div>
                                                    # ENDIF #
                                                </div>
                                                <div class="game-team# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-px-100# ENDIF ## ENDIF #">
                                                    <div class="game-score away-score# IF NOT rounds.C_HAT_PLAYOFF ## IF NOT rounds.C_FINAL # width-pc-50# ELSE # width-px-50# ENDIF ## ELSE # width-px-50# ENDIF #">{rounds.matches.AWAY_SCORE}</div>
                                                    # IF NOT rounds.C_HAT_PLAYOFF #
                                                        # IF NOT rounds.C_FINAL #
                                                            <div class="game-score away-score width-pc-50">{rounds.matches.AWAY_SCORE_B}# IF rounds.matches.C_HAS_PEN # <span class="small">({rounds.matches.AWAY_PEN})</span># ENDIF #</div>
                                                        # ENDIF #
                                                    # ENDIF #
                                                </div>
                                            </div>
                                        </div>
                                    # END rounds.matches #
                                </div>
                            </div>
                        # END rounds #
                    </div>
                </div>
            # ELSE #
                # IF C_LOOSER_BRACKET #
                    <h3>{@football.looser.bracket}</h3>
                    <div class="looser-bracket">
                        <div class="cell-bracket">
                            # START l_rounds #
                                <div# IF l_rounds.C_ALL_PLACES # id="looser-main-round-{l_rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF l_rounds.C_ALL_PLACES # all-places# ENDIF #">
                                    <h5 class="bracket-round-title">{l_rounds.L_TITLE}</h5>
                                    <div class="bracket-round-matches">
                                        # START l_rounds.matches #
                                            <div id="{l_rounds.matches.MATCH_ID}" class="game-match">
                                                <div class="game-details small text-italic">
                                                    <span>{l_rounds.matches.PLAYGROUND}</span>
                                                    <span># IF C_ONE_DAY #{l_rounds.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{l_rounds.matches.MATCH_DATE_FULL}# ENDIF #</span>
                                                    <span>{l_rounds.matches.MATCH_ID}</span>
                                                </div>
                                                <div  class="id-{l_rounds.matches.HOME_ID} game-team game-home# IF l_rounds.matches.C_HOME_FAV # text-strong# ENDIF #"
                                                        # IF l_rounds.matches.C_HOME_WIN # style="background-color: {l_rounds.matches.WIN_COLOR}"# ENDIF #>
                                                    <div class="home-{l_rounds.matches.MATCH_ID} home-team">
                                                        # IF l_rounds.matches.HOME_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{l_rounds.matches.HOME_LOGO}" alt="{l_rounds.matches.HOME_TEAM}">
                                                                <span>{l_rounds.matches.HOME_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
                                                    </div>
                                                    <div class="game-score home-score width-px-50">{l_rounds.matches.HOME_SCORE}# IF l_rounds.matches.C_HAS_PEN # <span class="small">({l_rounds.matches.HOME_PEN})</span># ENDIF #</div>
                                                </div>
                                                <div class="id-{l_rounds.matches.AWAY_ID} game-team game-away# IF l_rounds.matches.C_AWAY_FAV # text-strong# ENDIF #"
                                                        # IF l_rounds.matches.C_AWAY_WIN # style="background-color: {l_rounds.matches.WIN_COLOR}"# ENDIF #>
                                                    <div class="away-{l_rounds.matches.MATCH_ID} away-team">
                                                        # IF l_rounds.matches.AWAY_ID #
                                                            <div class="flex-team flex-left">
                                                                <img src="{PATH_TO_ROOT}/{l_rounds.matches.AWAY_LOGO}" alt="{l_rounds.matches.AWAY_TEAM}">
                                                                <span>{l_rounds.matches.AWAY_TEAM}</span>
                                                            </div>
                                                        # ENDIF #
                                                    </div>
                                                    <div class="game-score away-score width-px-50">{l_rounds.matches.AWAY_SCORE}# IF l_rounds.matches.C_HAS_PEN # <span class="small">({l_rounds.matches.AWAY_PEN})</span># ENDIF #</div>
                                                </div>
                                            </div>
                                        # END l_rounds.matches #
                                    </div>
                                </div>
                            # END l_rounds #
                        </div>
                        <div class="cell-bracket">
                            # START l_rounds #
                                <div id="looser-sub-round-{l_rounds.ROUND_ID}" class="bracket-l_rounds">
                                    <div class="bracket-round-matches"></div>
                                </div>
                            # END l_rounds #
                        </div>
                    </div>
                # ENDIF #
                # IF C_LOOSER_BRACKET #<h3>{@football.winner.bracket}</h3># ENDIF #
                <div class="winner-bracket">
                    <div class="cell-bracket">
                        # START w_rounds #
                            <div# IF w_rounds.C_ALL_PLACES # id="winner-main-round-{w_rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF w_rounds.C_ALL_PLACES # all-places# ENDIF #">
                                <h5 class="bracket-round-title">{w_rounds.L_TITLE}</h5>
                                <div class="bracket-round-matches">
                                    # START w_rounds.matches #
                                        <div id="{w_rounds.matches.MATCH_ID}" class="game-match">
                                            <div class="game-details small text-italic">
                                                <span>{w_rounds.matches.PLAYGROUND}</span>
                                                <span># IF C_ONE_DAY #{w_rounds.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{w_rounds.matches.MATCH_DATE_FULL}# ENDIF #</span>
                                                <span>{w_rounds.matches.MATCH_ID}</span>
                                            </div>
                                            <div class="id-{w_rounds.matches.HOME_ID} game-team game-home# IF w_rounds.matches.C_HOME_FAV # text-strong# ENDIF #"
                                                    # IF C_HOME_WIN # style="background-color: {w_rounds.matches.WIN_COLOR}"# ENDIF #>
                                                <div class="home-{w_rounds.matches.MATCH_ID} home-team">
                                                    # IF w_rounds.matches.HOME_ID #
                                                        <div class="flex-team flex-left">
                                                            <img src="{PATH_TO_ROOT}/{w_rounds.matches.HOME_LOGO}" alt="{w_rounds.matches.HOME_TEAM}">
                                                            <span>{w_rounds.matches.HOME_TEAM}</span>
                                                        </div>
                                                    # ENDIF #
                                                </div>
                                                <div class="game-score home-score width-px-50 align-center">{w_rounds.matches.HOME_SCORE}# IF w_rounds.matches.C_HAS_PEN # <span class="small">({w_rounds.matches.HOME_PEN})</span># ENDIF #</div>
                                            </div>
                                            <div class="id-{w_rounds.matches.AWAY_ID} game-team game-away# IF w_rounds.matches.C_AWAY_FAV # text-strong# ENDIF #"
                                                    # IF C_AWAY_WIN # style="background-color: {w_rounds.matches.WIN_COLOR}"# ENDIF #>
                                                <div class="away-{w_rounds.matches.MATCH_ID} away-team">
                                                    # IF w_rounds.matches.AWAY_ID #
                                                        <div class="flex-team flex-left">
                                                            <img src="{PATH_TO_ROOT}/{w_rounds.matches.AWAY_LOGO}" alt="{w_rounds.matches.AWAY_TEAM}">
                                                            <span>{w_rounds.matches.AWAY_TEAM}</span>
                                                        </div>
                                                    # ENDIF #
                                                </div>
                                                <div class="game-score away-score width-px-50 align-center">{w_rounds.matches.AWAY_SCORE}# IF w_rounds.matches.C_HAS_PEN # <span class="small">({w_rounds.matches.AWAY_PEN})</span># ENDIF #</div>
                                            </div>
                                        </div>
                                    # END w_rounds.matches #
                                </div>
                            </div>
                        # END w_rounds #
                    </div>
                    # IF C_LOOSER_BRACKET #
                        <div class="cell-bracket">
                            # START w_rounds #
                                <div id="winner-sub-round-{w_rounds.ROUND_ID}" class="bracket-w_rounds">
                                    <div class="bracket-round-matches"></div>
                                </div>
                            # END w_rounds #
                        </div>
                    # ENDIF #
                </div>
            # ENDIF #
            # INCLUDE JS_DOC #
        # ELSE #
            <div class="message-helper bgc notice">{@football.message.no.matches}</div>
        # ENDIF #
    </div>
</article>

<script src="{PATH_TO_ROOT}/football/templates/js/football.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/football/templates/js/football.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
# IF C_LOOSER_BRACKET #
    <script>
        function move_matches(target) {
            let elements = document.querySelectorAll('[id*="' + target + '-main-round-"]');

            elements.forEach(element => {
                let idName = element.getAttribute('id');
                let split = idName.split('-');
                let id = split[split.length - 1];
                let mainRound = document.querySelector('#' + target + '-main-round-' + id + '');
                let subRound = document.querySelector('#' + target + '-sub-round-' + id + '');
                let matchCount = mainRound.querySelectorAll('.game-match').length;

                if (matchCount >= 2) {
                    let lastTwoMatches = Array.from(mainRound.querySelectorAll('.game-match')).slice(matchCount - (matchCount / 2), matchCount);
                    lastTwoMatches.forEach(match => subRound.appendChild(match));
                }
            });
        }
        move_matches('winner');
        move_matches('looser');
    </script>
# ENDIF #