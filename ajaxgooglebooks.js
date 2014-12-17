/*jslint browser:true */
/*global $:false */
$(document).ready(function () {
    'use strict';
    // owl carousel plugin start
    $('.owl-carousel').owlCarousel({
        //display only one item at a time
        singleItem: true,
        //automatically advance slides
        autoPlay: true,
        //stop autoPlay on hover
        stopOnHover: true
    });

    // loop through each topbooks-result div
    $('.topbooks-result').each(function () {
        // set the google api to query the isbn taken from the data-isbn attribute in the topbooks div
        var googleAPI = 'https:/www.googleapis.com/books/v1/volumes?q=isbn' + $(this).data("isbn") + '&amp;maxResults=1&amp;key=';

        // apikey configured from google developer's console
        var apiKey = 'AIzaSyCSPX6tvgDiK5rId6n9DdHtPyxhCwvEr9U';

        // references current topbooks-result div in ajax call below
        var self = $(this);

        // parse json data from google books
        $.getJSON(googleAPI + apiKey,
            function (response) {
                // returns first result from isbn query to google books api
                var item = response.items[0];

                var summaryText = item.searchInfo.textSnippet;

                // returns comma-separated list of authors if there are multiple authors
                var authors = item.volumeInfo.authors.join(', ');

                // overwrite data from alma with google books info. If the ajax request doesn't return anything, the data from alma acts as a fallback
                $(self).find('.topbooks-summary').html(summaryText);
                $(self).find('.topbooks-author').text(authors);
            });
    });
});
