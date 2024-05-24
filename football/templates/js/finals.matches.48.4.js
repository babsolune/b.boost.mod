// World Cup like
// @copyright   &copy; 2005-2023 PHPBoost
// @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
// @author      Sebastien LATIGUE <babsolune@phpboost.com>
// @version     PHPBoost 6.0 - last update: 2023 10 07
// @since       PHPBoost 6.0 - 2023 10 07

jQuery(document).ready(function(){
    // Matches form

    jQuery('.WH1 > div').append('<br />1A|2C'); // 38
    jQuery('.WH2 > div').append('<br />2A|2B'); // 37
    jQuery('.WH3 > div').append('<br />1B|3A/D/E/F'); // 40
    jQuery('.WH4 > div').append('<br />1C|3D/E/F'); // 39
    jQuery('.WH5 > div').append('<br />1F|3A/B/C'); // 42
    jQuery('.WH6 > div').append('<br />2D|2E'); // 41
    jQuery('.WH7 > div').append('<br />1E|3A/B/C/D'); // 43
    jQuery('.WH8 > div').append('<br />1D|2F'); // 44

    jQuery('.WQ1 > div').append('<br />WH4|WH2'); // 45
    jQuery('.WQ2 > div').append('<br />WH5|WH6'); // 46
    jQuery('.WQ3 > div').append('<br />WH3|WH1'); // 48
    jQuery('.WQ4 > div').append('<br />WH7|WH8'); // 47

    jQuery('.WD1 > div').append('<br />WQ1|WQ2'); // 49
    jQuery('.WD2 > div').append('<br />WQ4|WQ3'); // 50

    jQuery('.WF1 > div').append(' - 1/2<br />WD1|WD2'); // 51
    jQuery('.WF2 > div').append(' - 3/4<br />perdant WD1<br />perdant WD2'); // 52

    // Matches front

    if(jQuery('.home-W31').text() == '') jQuery('.home-W31').append('1e du groupe A');
    if(jQuery('.away-W31').text() == '') jQuery('.away-W31').append('2e du groupe D');
    if(jQuery('.home-W32').text() == '') jQuery('.home-W32').append('1e du groupe B');
    if(jQuery('.away-W32').text() == '') jQuery('.away-W32').append('2e du groupe C');
    if(jQuery('.home-W33').text() == '') jQuery('.home-W33').append('1e du groupe C');
    if(jQuery('.away-W33').text() == '') jQuery('.away-W33').append('2e du groupe B');
    if(jQuery('.home-W34').text() == '') jQuery('.home-W34').append('1e du groupe D');
    if(jQuery('.away-W34').text() == '') jQuery('.away-W34').append('2e du groupe A');

    if(jQuery('.home-W21').text() == '') jQuery('.home-W21').append('perdant W31');
    if(jQuery('.away-W21').text() == '') jQuery('.away-W21').append('perdant W32');
    if(jQuery('.home-W22').text() == '') jQuery('.home-W22').append('perdant W33');
    if(jQuery('.away-W22').text() == '') jQuery('.away-W22').append('perdant W34');
    if(jQuery('.home-W23').text() == '') jQuery('.home-W23').append('gagnant W31');
    if(jQuery('.away-W23').text() == '') jQuery('.away-W23').append('gagnant W32');
    if(jQuery('.home-W24').text() == '') jQuery('.home-W24').append('gagnant W33');
    if(jQuery('.away-W24').text() == '') jQuery('.away-W24').append('gagnant W34');

    if(jQuery('.home-W11').text() == '') jQuery('.home-W11').append('perdant W21');
    if(jQuery('.away-W11').text() == '') jQuery('.away-W11').append('perdant W22');
    if(jQuery('.home-W12').text() == '') jQuery('.home-W12').append('perdant W23');
    if(jQuery('.away-W12').text() == '') jQuery('.away-W12').append('perdant W24');
    if(jQuery('.home-W13').text() == '') jQuery('.home-W13').append('gagnant W21');
    if(jQuery('.away-W13').text() == '') jQuery('.away-W13').append('gagnant W22');
    if(jQuery('.home-W14').text() == '') jQuery('.home-W14').append('gagnant W23');
    if(jQuery('.away-W14').text() == '') jQuery('.away-W14').append('gagnant W24');
});