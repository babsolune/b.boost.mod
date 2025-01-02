# IF C_HAS_GAMES #
    <article class="games cell-flex cell-columns-2">
        # INCLUDE GAMES_LIST #
    </article>
# ELSE #
    <div class="message-helper bgc notice">{@scm.message.no.games}</div>
# ENDIF #
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.width# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.highlight# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>
<script src="{PATH_TO_ROOT}/scm/templates/js/scm.event.home# IF C_CSS_CACHE_ENABLED #.min# ENDIF #.js"></script>