# INCLUDE MENU #
# IF C_ROUNDS_16_4 #
        <h2>{@football.menu.compet.bracket}</h2>
    # IF C_LOOSER_BRACKET #
        <h3>{@football.looser.bracket}</h3>
        <div class="cell-flex cell-columns-4 finals-round-title">
            <h5>{@football.place} 9-16</h5>
            <h5>{@football.place} 9-12</h5>
            <h5>{@football.place} 11-12</h5>
            <h5>{@football.place} 9-10</h5>
        </div>
        <div class="cell-flex cell-columns-4 finals-round">
            <div class="pl-9-16">
                # IF C_M_L31 # <div># INCLUDE MATCH_L31 #</div> # ENDIF #
                # IF C_M_L32 # <div># INCLUDE MATCH_L32 #</div> # ENDIF #
                # IF C_M_L33 # <div># INCLUDE MATCH_L33 #</div> # ENDIF #
                # IF C_M_L34 # <div># INCLUDE MATCH_L34 #</div> # ENDIF #
            </div>
            <div class="pl-9-12">
                    # IF C_M_L23 # <div># INCLUDE MATCH_L23 #</div> # ENDIF #
                    # IF C_M_L24 # <div># INCLUDE MATCH_L24 #</div> # ENDIF #
            </div>
            <div class="pl-11-12">
                # IF C_M_L13 # <div># INCLUDE MATCH_L13 #</div> # ENDIF #
            </div>
            <div class="pl-9-10">
                # IF C_M_L14 # <div># INCLUDE MATCH_L14 #</div> # ENDIF #
            </div>
        </div>
        # IF C_ALL_PLACES #
            <div class="cell-flex cell-columns-4 finals-round-title">
                <div></div>
                <h5>{@football.place} 13-16</h5>
                <h5>{@football.place} 15-16</h5>
                <h5>{@football.place} 13-14</h5>
            </div>
            <div class="cell-flex cell-columns-4 finals-round">
                <div></div>
                <div class="pl-13-16">
                    # IF C_M_L21 # <div># INCLUDE MATCH_L21 #</div> # ENDIF #
                    # IF C_M_L22 # <div># INCLUDE MATCH_L22 #</div> # ENDIF #
                </div>
                <div class="pl-15-16">
                    # IF C_M_L11 # <div># INCLUDE MATCH_L11 #</div> # ENDIF #
                </div>
                <div class="pl-13-14">
                    # IF C_M_L12 # <div># INCLUDE MATCH_L12 #</div> # ENDIF #
                </div>
            </div>
        # ENDIF #
    # ENDIF #

    # IF C_LOOSER_BRACKET #<h3>{@football.winner.bracket}</h3># ENDIF #
    <div class="cell-flex cell-columns-4 finals-round-title">
        <h5># IF C_ALL_PLACES #{@football.place} 1-8# ELSE #{@football.round.of.8}# ENDIF #</h5>
        <h5># IF C_ALL_PLACES #{@football.place} 1-4# ELSE #{@football.round.of.4}# ENDIF #</h5>
        <h5># IF C_ALL_PLACES #{@football.place} 3-4# ELSE #{@football.round.of.2}# ENDIF #</h5>
        <h5># IF C_ALL_PLACES #{@football.place} 1-2# ELSE #{@football.round.of.1}# ENDIF #</h5>
    </div>
    <div class="cell-flex cell-columns-4 finals-round">
        <div class="pl-1-8">
            # IF C_M_W31 # <div># INCLUDE MATCH_W31 #</div> # ENDIF #
            # IF C_M_W32 # <div># INCLUDE MATCH_W32 #</div> # ENDIF #
            # IF C_M_W33 # <div># INCLUDE MATCH_W33 #</div> # ENDIF #
            # IF C_M_W34 # <div># INCLUDE MATCH_W34 #</div> # ENDIF #
        </div>
        <div class="pl-1-4">
            # IF C_ALL_PLACES #
                # IF C_M_W23 # <div># INCLUDE MATCH_W23 #</div> # ENDIF #
                # IF C_M_W24 # <div># INCLUDE MATCH_W24 #</div> # ENDIF #
            # ELSE #
                # IF C_M_W21 # <div># INCLUDE MATCH_W21 #</div> # ENDIF #
                # IF C_M_W22 # <div># INCLUDE MATCH_W22 #</div> # ENDIF #
            # ENDIF #
        </div>
        # IF C_ALL_PLACES #
            <div class="pl-3-4">
                # IF C_M_W13 # <div># INCLUDE MATCH_W13 #</div> # ENDIF #
            </div>
        # ENDIF #
        <div class="pl-1-2">
            # IF C_ALL_PLACES #
                # IF C_M_W14 # <div># INCLUDE MATCH_W14 #</div> # ENDIF #
            # ELSE #
                # IF C_THRID_PLACE #
                    <div>
                        # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
                        # IF C_M_W12 # <div># INCLUDE MATCH_W12 #</div> # ENDIF #
                    </div>
                # ELSE #
                    # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
                # ENDIF #
            # ENDIF #
        </div>
    </div>
    # IF C_ALL_PLACES #
        <div class="cell-flex cell-columns-4 finals-round-title">
            <div></div>
            <h5>{@football.place} 5-8</h5>
            <h5>{@football.place} 7-8</h5>
            <h5>{@football.place} 5-6</h5>
        </div>
        <div class="cell-flex cell-columns-4 finals-round">
            <div></div>
            <div class="pl-5-8">
                # IF C_M_W21 # <div># INCLUDE MATCH_W21 #</div> # ENDIF #
                # IF C_M_W22 # <div># INCLUDE MATCH_W22 #</div> # ENDIF #
            </div>
            <div class="pl-7-8">
                # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
            </div>
            <div class="pl-5-6">
                # IF C_M_W12 # <div># INCLUDE MATCH_W12 #</div> # ENDIF #
            </div>
        </div>
    # ENDIF #
    <script src="{PATH_TO_ROOT}/football/templates/js/finals.matches.16.4# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
# ENDIF #
# IF C_ROUNDS_24_4 #
    <h2>{@football.menu.compet.bracket}</h2>
    <div class="cell-flex cell-columns-4 finals-round-title">
            <h5>{@football.round.of.8}</h5>
            <h5>{@football.round.of.4}</h5>
            <h5>{@football.round.of.2}</h5>
            <h5>{@football.round.of.1}</h5>
    </div>
    <div class="cell-flex cell-columns-4 finals-round">
        <div class="pl-1-8">
            # IF C_M_W44 # <div># INCLUDE MATCH_W44 #</div> # ENDIF #
            # IF C_M_W42 # <div># INCLUDE MATCH_W42 #</div> # ENDIF #
            # IF C_M_W45 # <div># INCLUDE MATCH_W45 #</div> # ENDIF #
            # IF C_M_W46 # <div># INCLUDE MATCH_W46 #</div> # ENDIF #
            # IF C_M_W43 # <div># INCLUDE MATCH_W43 #</div> # ENDIF #
            # IF C_M_W41 # <div># INCLUDE MATCH_W41 #</div> # ENDIF #
            # IF C_M_W47 # <div># INCLUDE MATCH_W47 #</div> # ENDIF #
            # IF C_M_W48 # <div># INCLUDE MATCH_W48 #</div> # ENDIF #
        </div>
        <div class="pl-1-4">
            # IF C_M_W31 # <div># INCLUDE MATCH_W31 #</div> # ENDIF #
            # IF C_M_W32 # <div># INCLUDE MATCH_W32 #</div> # ENDIF #
            # IF C_M_W33 # <div># INCLUDE MATCH_W33 #</div> # ENDIF #
            # IF C_M_W34 # <div># INCLUDE MATCH_W34 #</div> # ENDIF #
        </div>
        <div class="pl-3-4">
            # IF C_M_W21 # <div># INCLUDE MATCH_W21 #</div> # ENDIF #
            # IF C_M_W22 # <div># INCLUDE MATCH_W22 #</div> # ENDIF #
        </div>
        <div class="pl-1-2">
            # IF C_THRID_PLACE #
                <div>
                    # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
                    # IF C_M_W12 # <div># INCLUDE MATCH_W12 #</div> # ENDIF #
                </div>
            # ELSE #
                # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
            # ENDIF #
        </div>
    </div>
    <script src="{PATH_TO_ROOT}/football/templates/js/finals.matches.24.4# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
# ENDIF #
# IF C_ROUNDS_32_4 #
    <h2>{@football.menu.compet.bracket}</h2>
    <div class="cell-flex cell-columns-4 finals-round-title">
            <h5>{@football.round.of.8}</h5>
            <h5>{@football.round.of.4}</h5>
            <h5>{@football.round.of.2}</h5>
            <h5>{@football.round.of.1}</h5>
    </div>
    <div class="cell-flex cell-columns-4 finals-round">
        <div class="pl-1-8">
            # IF C_M_W41 # <div># INCLUDE MATCH_W41 #</div> # ENDIF #
            # IF C_M_W42 # <div># INCLUDE MATCH_W42 #</div> # ENDIF #
            # IF C_M_W43 # <div># INCLUDE MATCH_W43 #</div> # ENDIF #
            # IF C_M_W44 # <div># INCLUDE MATCH_W44 #</div> # ENDIF #
            # IF C_M_W45 # <div># INCLUDE MATCH_W45 #</div> # ENDIF #
            # IF C_M_W46 # <div># INCLUDE MATCH_W46 #</div> # ENDIF #
            # IF C_M_W47 # <div># INCLUDE MATCH_W47 #</div> # ENDIF #
            # IF C_M_W48 # <div># INCLUDE MATCH_W48 #</div> # ENDIF #
        </div>
        <div class="pl-1-4">
            # IF C_M_W31 # <div># INCLUDE MATCH_W31 #</div> # ENDIF #
            # IF C_M_W32 # <div># INCLUDE MATCH_W32 #</div> # ENDIF #
            # IF C_M_W33 # <div># INCLUDE MATCH_W33 #</div> # ENDIF #
            # IF C_M_W34 # <div># INCLUDE MATCH_W34 #</div> # ENDIF #
        </div>
        <div class="pl-3-4">
            # IF C_M_W21 # <div># INCLUDE MATCH_W21 #</div> # ENDIF #
            # IF C_M_W22 # <div># INCLUDE MATCH_W22 #</div> # ENDIF #
        </div>
        <div class="pl-1-2">
            # IF C_THRID_PLACE #
                <div>
                    # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
                    # IF C_M_W12 # <div># INCLUDE MATCH_W12 #</div> # ENDIF #
                </div>
            # ELSE #
                # IF C_M_W11 # <div># INCLUDE MATCH_W11 #</div> # ENDIF #
            # ENDIF #
        </div>
    </div>
    <script src="{PATH_TO_ROOT}/football/templates/js/finals.matches.32.4# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
# ENDIF #
<script src="{PATH_TO_ROOT}/football/templates/js/finals.matches# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>