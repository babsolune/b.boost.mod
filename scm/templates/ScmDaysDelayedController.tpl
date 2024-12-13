<section id="module-scm" class="several-items">
    # INCLUDE MENU #
    <article class="days-delayed">
        <header><h2>{@scm.delayed.days}</h2></header>
        # IF C_ITEMS #
            <div class="cell-flex cell-columns-2 cell-tile">
                # START dates #
                    <div class="cell">
                        <div class="cell-header">
                            <h5 class="cell-name">{dates.DATE}</h5>
                        </div>
                        <div class="cell-list">
                            <ul>
                                # START dates.items #
                                    <li class="li-stretch# IF dates.items.C_OVERTIME # bgc warning# ENDIF #">
                                        <a href="{dates.items.U_DAY}" class="offload">{@scm.day} {dates.items.CLUSTER}</a>
                                        <span>{dates.items.HOME_TEAM} <strong>vs</strong> {dates.items.AWAY_TEAM}</span>
                                    </li>
                                # END dates.items #
                            </ul>
                        </div>
                    </div>
                # END days #
            </div>
        # ELSE #
            <div class="message-helper bgc notice">{@scm.message.no.games}</div>
        # ENDIF #
    </article>
    <footer></footer>
</section>
