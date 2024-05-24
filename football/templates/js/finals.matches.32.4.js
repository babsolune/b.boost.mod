// local tournament
// @copyright   &copy; 2005-2023 perdant LHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LATIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07

jQuery(document).ready(function(){
    // Matches form
    jQuery('.WH1 > div').append('<br />1A|2B');
    jQuery('.WH2 > div').append('<br />1C|2D');
    jQuery('.WH3 > div').append('<br />1E|2F');
    jQuery('.WH4 > div').append('<br />1G|2H');
    jQuery('.WH5 > div').append('<br />1B|2A');
    jQuery('.WH6 > div').append('<br />1D|2C');
    jQuery('.WH7 > div').append('<br />1F|2E');
    jQuery('.WH8 > div').append('<br />1H|2G');

    jQuery('.WQ1 > div').append('gagnant WH1<br />gagnant WH2');
    jQuery('.WQ2 > div').append('gagnant WH2<br />gagnant WH3');
    jQuery('.WQ3 > div').append('gagnant WH4<br />gagnant WH5');
    jQuery('.WQ4 > div').append('gagnant WH6<br />gagnant WH7');

    jQuery('.WD1 > div').append('gagnant WQ1<br />gagnant WQ2');
    jQuery('.WD2 > div').append('gagnant WQ3<br />gagnant WQ4');

    jQuery('.WF1 > div').append('gagnant WD1<br />gagnant WD2');
    jQuery('.WF2 > div').append('perdant WD1<br />perdant WD2');

    // Matches front
    if(jQuery('.home-W41').text() == '') jQuery('.home-W41').append('1e du groupe A');
    if(jQuery('.away-W41').text() == '') jQuery('.away-W41').append('2e du groupe B');
    if(jQuery('.home-W42').text() == '') jQuery('.home-W42').append('1e du groupe C');
    if(jQuery('.away-W42').text() == '') jQuery('.away-W42').append('2e du groupe D');
    if(jQuery('.home-W43').text() == '') jQuery('.home-W43').append('1e du groupe E');
    if(jQuery('.away-W43').text() == '') jQuery('.away-W43').append('2e du groupe F');
    if(jQuery('.home-W44').text() == '') jQuery('.home-W44').append('1e du groupe G');
    if(jQuery('.away-W44').text() == '') jQuery('.away-W44').append('2e du groupe H');
    if(jQuery('.home-W45').text() == '') jQuery('.home-W45').append('1e du groupe B');
    if(jQuery('.away-W45').text() == '') jQuery('.away-W45').append('2e du groupe A');
    if(jQuery('.home-W46').text() == '') jQuery('.home-W46').append('1e du groupe D');
    if(jQuery('.away-W46').text() == '') jQuery('.away-W46').append('2e du groupe C');
    if(jQuery('.home-W47').text() == '') jQuery('.home-W47').append('1e du groupe F');
    if(jQuery('.away-W47').text() == '') jQuery('.away-W47').append('2e du groupe E');
    if(jQuery('.home-W48').text() == '') jQuery('.home-W48').append('1e du groupe H');
    if(jQuery('.away-W48').text() == '') jQuery('.away-W48').append('2e du groupe G');

    if(jQuery('.home-W31').text() == '') jQuery('.home-W31').append('gagnant W41');
    if(jQuery('.away-W31').text() == '') jQuery('.away-W31').append('gagnant W42');
    if(jQuery('.home-W32').text() == '') jQuery('.home-W32').append('gagnant W43');
    if(jQuery('.away-W32').text() == '') jQuery('.away-W32').append('gagnant W44');
    if(jQuery('.home-W33').text() == '') jQuery('.home-W33').append('gagnant W45');
    if(jQuery('.away-W33').text() == '') jQuery('.away-W33').append('gagnant W46');
    if(jQuery('.home-W34').text() == '') jQuery('.home-W34').append('gagnant W47');
    if(jQuery('.away-W34').text() == '') jQuery('.away-W34').append('gagnant W48');

    if(jQuery('.home-W21').text() == '') jQuery('.home-W21').append('gagnant W31');
    if(jQuery('.away-W21').text() == '') jQuery('.away-W21').append('gagnant W32');
    if(jQuery('.home-W22').text() == '') jQuery('.home-W22').append('gagnant W33');
    if(jQuery('.away-W22').text() == '') jQuery('.away-W22').append('gagnant W34');

    if(jQuery('.home-W11').text() == '') jQuery('.home-W11').append('gagnant W21');
    if(jQuery('.away-W11').text() == '') jQuery('.away-W11').append('gagnant W22');
    if(jQuery('.home-W12').text() == '') jQuery('.home-W12').append('perdant W21');
    if(jQuery('.away-W12').text() == '') jQuery('.away-W12').append('perdant W22');
});