/* Plugins jQuery
-------------------------------------------------- */
<!--#include file="./jquery-1.10.2.min.js" -->
<!--#include file="./jquery-migrate-1.2.1.min.js" -->
<!--#include file="./jquery-ui.min.js" -->
<!--#include file="./jquery.fullcalendar.js" -->
<!--#include file="./jquery.autocomplete.min.js" -->
<!--#include file="./jquery.timeentry.js" -->
<!--#include file="./jquery.elastic.js" -->
<!--#include file="./jquery.toggletext.js" -->
<!--#include file="./jquery.multiselect.min.js" -->
<!--#include file="./jquery.tablesorter.js" -->
<!--#include file="./jquery.tablesorter.pager.js" -->
<!--#include file="./jquery.tipsy.js" -->
<!--#include file="./jquery.fancybox-1.3.4.js" -->
<!--#include file="./bootstrap.min.js" -->
<!--#include file="./dropzone.min.js" -->

document.addEventListener("DOMContentLoaded", function() {
    // Titres
    $('a[title]').tipsy({live: true});
    $('abbr[title]').tipsy({live: true});
    
}, false);