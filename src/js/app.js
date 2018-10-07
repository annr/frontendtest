var fet = {
    title: 'FrontendTest',
    container: $('#results'),
    suggestionTemplate: document.querySelector('.suggestionTemplate'),
    errorMsg: $('#errorMessage'),
    loading: $('.loading'),
    resultsTitle: $('#resultsTitle'),
    analysisSteps: $('#analysisSteps'),
    congratulations: $('.congratulations'),
    genericErrorMessage: 'Sorry 😬. An error occurred.',
    testString: 'giraffe',
    getResultsTitle: function (url) {
        return 'Suggestions for <span class="urlToProcess">' + url + '</span>'
    },
    unsetMessages: function () {
        fet.container.html('');
        fet.errorMsg.html('');
        fet.analysisSteps.html('');
    },
    showError: function (err) {
        if (err) {
            fet.errorMsg.html(err);
        } else {
            fet.errorMsg.html(fet.genericErrorMessage);
        }
        fet.loading.hide();
    },
    showResults: function (results) {
        if (results && !results.status) {
            if (Array.isArray(results) && results.length > 0) {
                results.forEach(function (sug) {
                    fet.populateAndAddSuggestion(sug);
                });
            }
        }
    }
};

$(document).ready(function () {
    $("#testFrontend").submit(function (event) {
        event.preventDefault();
        // this will never happen, and is not necessary.
        if (!$('#url').val()) {
            fet.errorMsg.html('No URL provided.');
            return;
        }

        fet.resultsTitle.html(fet.getResultsTitle($('#url').val()));
        fet.unsetMessages();
        fet.loading.show();

        // first pass: request page, check SSL, avoid redirects.
        const firstPass = "src/php/requestPage.php?url=";
        $.ajax({
            type: 'POST',
            url: firstPass + $('#url').val(),
            data: $("#testFrontend").serialize(),
            error: function (err) {
                fet.showError(err);
            },
            success: function (results) {
                // we only include 'status' if something goes wrong.
                if (results && !results.status) {

                    fet.showResults(results);
                    $.ajax({
                        type: "POST",
                        url: "src/php/analyzeHTMLHead.php?url=" + $('#url').val(),
                        error: function (err) {
                            fet.showError(err);
                        },
                        success: function (results) {
                            fet.showResults(results);
                        }
                    });

                    $.ajax({
                        type: "POST",
                        url: "src/php/validateHTML.php?url=" + $('#url').val(),
                        error: function (err) {
                            fet.showError(err);
                        },
                        success: function (results) {
                            if (results && !results.status) {

                                fet.showResults(results);
                                // we are going to assume processing the body with PHP is going to take longer than anything.
                                // so after these suggestions are processed, well hide the spinner.
                                fet.loading.fadeOut();

                                // finally, run rule tests that are slow.
                                // put working indicator and messsage at bottom.
                                fet.analysisSteps.show();
                                fet.analysisSteps.addClass('animatedEllipsis');
                                fet.analysisSteps.html('Running final tests');

                                $.ajax({
                                    type: "POST",
                                    url: "src/php/analyzeHTMLBody.php?url=" + $('#url').val(),
                                    error: function (err) {
                                        fet.showError(err);
                                    },
                                    success: function (results) {
                                        fet.showResults(results);
                                    }
                                });

                                $.ajax({
                                    type: "POST",
                                    url: "src/php/analyzeSlowFinal.php?url=" + $('#url').val(),
                                    error: function (err) {
                                        fet.showError(err);
                                    },
                                    success: function (results) {
                                        fet.showResults(results);
                                        // if no suggestions were found, congratulate.
                                        fet.analysisSteps.removeClass('animatedEllipsis');
                                        fet.analysisSteps.html('Done.');
                                        if (fet.container.find('.suggestionTemplate').length === 0) {
                                            fet.congratulations.show();
                                        }
                                    }
                                });

                            }
                        }

                    });
                } else {
                    fet.analysisSteps.html('');
                    fet.resultsTitle.html('');
                    if (results && results.curl_error) {
                        fet.errorMsg.html(results.curl_error);
                    } else {
                        fet.errorMsg.html('Error requesting page.');
                    }
                    fet.loading.fadeOut();
                }

            }
        });
    });

});

// TODO: Update this part to use jQuery
fet.populateAndAddSuggestion = function (sug) {
    // bad mix of jQuery and raw
    var suggestion = fet.suggestionTemplate.cloneNode(true);
    //var levels = ['bd-callout-info', 'bd-callout-warning', 'bd-callout-error'];
    var calloutClass = 'bd-callout-warning';

    // we don't bother with a default or use info yet.
    if (sug.weight >= 70) {
        // whoa baby
        calloutClass = 'bd-callout-danger';
    }

    suggestion.className = suggestion.className + ' ' + calloutClass;
    suggestion.querySelector('.suggestion-title').textContent = sug.title;

    // w3c descriptions can be optional, so only show the element if a desc comes back 
    if (!sug.description) {
        suggestion.querySelector('.suggestion-description').className = 'suggestion-description hidden-suggestion-desc'
    }
    suggestion.querySelector('.suggestion-description').innerHTML = sug.description;
    suggestion.removeAttribute('hidden');
    fet.container.append(suggestion);
};
