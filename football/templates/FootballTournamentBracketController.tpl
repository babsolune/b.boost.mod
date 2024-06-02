# INCLUDE MENU #
<h2>{@football.matches.bracket.stage}</h2>
# IF C_HAS_MATCHES #
    # IF C_LOOSER_BRACKET #
        <h3>{@football.looser.bracket}</h3>
        <div class="looser-bracket">
            <div class="cell-bracket">
                # START l_rounds #
                    <div# IF l_rounds.C_ALL_PLACES # id="looser-main-round-{l_rounds.ROUND_ID}"# ENDIF # class="bracket-round# IF l_rounds.C_ALL_PLACES # all-places# ENDIF #">
                        <h5 class="bracket-round-title">{l_rounds.L_TITLE}</h5>
                        <div class="bracket-round-matches">
                            # START l_rounds.matches #
                                <div id="{l_rounds.matches.MATCH_ID}" class="bracket-match">
                                    <div class="bracket-details bgc link-color small text-italic">
                                        <span>{l_rounds.matches.PLAYGROUND}</span>
                                        <span># IF C_ONE_DAY #{l_rounds.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{l_rounds.matches.MATCH_DATE_FULL}# ENDIF #</span>
                                        <span>{l_rounds.matches.MATCH_ID}</span>
                                    </div>
                                    <div  class="id-{l_rounds.matches.HOME_ID} bracket-team bracket-home# IF l_rounds.matches.C_HOME_FAV # text-strong# ENDIF #"
                                            # IF C_HOME_WIN # style="background-color: {l_rounds.matches.WIN_COLOR}"# ENDIF #>
                                        <div class="home-{l_rounds.matches.MATCH_ID} home-team width-pc-70">{l_rounds.matches.HOME_TEAM}</div>
                                        <div class="bracket-score home-score width-pc-30">{l_rounds.matches.HOME_SCORE}# IF l_rounds.matches.C_HAS_PEN # <span class="small">({l_rounds.matches.HOME_PEN})</span># ENDIF #</div>
                                    </div>
                                    <div class="id-{l_rounds.matches.AWAY_ID} bracket-team bracket-away# IF l_rounds.matches.C_AWAY_FAV # text-strong# ENDIF #"
                                            # IF C_AWAY_WIN # style="background-color: {l_rounds.matches.WIN_COLOR}"# ENDIF #>
                                        <div class="away-{l_rounds.matches.MATCH_ID} away-team width-pc-70">{l_rounds.matches.AWAY_TEAM}</div>
                                        <div class="bracket-score away-score width-pc-30">{l_rounds.matches.AWAY_SCORE}# IF l_rounds.matches.C_HAS_PEN # <span class="small">({l_rounds.matches.AWAY_PEN})</span># ENDIF #</div>
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
                            <div id="{w_rounds.matches.MATCH_ID}" class="bracket-match">
                                <div class="bracket-details bgc link-color small text-italic">
                                    <span>{w_rounds.matches.PLAYGROUND}</span>
                                    <span># IF C_ONE_DAY #{w_rounds.matches.MATCH_DATE_HOUR_MINUTE}# ELSE #{w_rounds.matches.MATCH_DATE_FULL}# ENDIF #</span>
                                    <span>{w_rounds.matches.MATCH_ID}</span>
                                </div>
                                <div class="id-{w_rounds.matches.HOME_ID} bracket-team bracket-home# IF w_rounds.matches.C_HOME_FAV # text-strong# ENDIF #"
                                        # IF C_HOME_WIN # style="background-color: {w_rounds.matches.WIN_COLOR}"# ENDIF #>
                                    <div class="home-{w_rounds.matches.MATCH_ID} home-team width-pc-70">{w_rounds.matches.HOME_TEAM}</div>
                                    <div class="bracket-score home-score width-pc-30 align-center">{w_rounds.matches.HOME_SCORE}# IF w_rounds.matches.C_HAS_PEN # <span class="small">({w_rounds.matches.HOME_PEN})</span># ENDIF #</div>
                                </div>
                                <div class="id-{w_rounds.matches.AWAY_ID} bracket-team bracket-away# IF w_rounds.matches.C_AWAY_FAV # text-strong# ENDIF #"
                                        # IF C_AWAY_WIN # style="background-color: {w_rounds.matches.WIN_COLOR}"# ENDIF #>
                                    <div class="away-{w_rounds.matches.MATCH_ID} away-team width-pc-70">{w_rounds.matches.AWAY_TEAM}</div>
                                    <div class="bracket-score away-score width-pc-30 align-center">{w_rounds.matches.AWAY_SCORE}# IF w_rounds.matches.C_HAS_PEN # <span class="small">({w_rounds.matches.AWAY_PEN})</span># ENDIF #</div>
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
    # INCLUDE JS_DOC #
# ELSE #
    yenapadmatch
# ENDIF #
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
                let matchCount = mainRound.querySelectorAll('.bracket-match').length;

                if (matchCount >= 2) {
                    let lastTwoMatches = Array.from(mainRound.querySelectorAll('.bracket-match')).slice(matchCount - (matchCount / 2), matchCount);
                    lastTwoMatches.forEach(match => subRound.appendChild(match));
                }
            });
        }
        move_matches('winner');
        move_matches('looser');
    </script>
# ENDIF #