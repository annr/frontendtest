var fet = {
  title: 'FrontendTest',
  container: $('#results'),
  suggestionTemplate: document.querySelector('.suggestionTemplate'),
  errorMsg: $('#errorMessage'),
  loading: $('.loading'),
  resultsTitle: $('#resultsTitle'),
  analysisSteps: $('#analysisSteps'),
  congratulations: $('.congratulations'),
};

$(document).ready(function () {
  $("#testFrontend").submit(function (event) {
    event.preventDefault();

    fet.resultsTitle.html('Suggestions for <span class="urlToProcess">' + $('#url').val() + '</span>:');
    fet.container.html('');
    fet.errorMsg.html('');
    fet.analysisSteps.html('');
    fet.loading.show();

    $.ajax({
      type: "POST",
      url: "src/php/requestPage.php?url=" + $('#url').val(),
      data: $("#testFrontend").serialize(),
      error: function (err) {
        fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
        fet.loading.hide();
      },
      success: function (data) {
        var res = JSON.parse(data);
        // we only include 'status' if something goes wrong.
        if (res && !res.status) {
          if (res.length > 0) {
            res.forEach(function (sug) {
              fet.populateAndAddSuggestion(sug);
            });
          }

          $.ajax({
            type: "POST",
            url: "src/php/analyzeHTMLHead.php?url=" + $('#url').val(),
            error: function (err) {
              fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
              fet.loading.hide();
            },
            success: function (results) {
              // Splits off from how we did the outer tests, and is a mess.
              var res = JSON.parse(results);
              if (res && !res.status) {
                if (res.length > 0) {
                  res.forEach(function (sug) {
                    fet.populateAndAddSuggestion(sug);
                  });
                }
              }
            }
          });

          $.ajax({
            type: "POST",
            url: "src/php/analyzeHTMLBody.php?url=" + $('#url').val(),
            error: function (err) {
              fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
              fet.loading.hide();
            },
            success: function (results) {
              // Splits off from how we did the outer tests, and is a mess.
              var res = JSON.parse(results);
              if (res && !res.status) {
                if (res.length > 0) {
                  res.forEach(function (sug) {
                    fet.populateAndAddSuggestion(sug);
                  });
                }
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
                  url: "src/php/analyzeSlowFinal.php?url=" + $('#url').val(),
                  error: function (err) {
                    fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
                    fet.loading.hide();
                    fet.analysisSteps.html('An error occurred.');
                  },
                  success: function (results) {
                    // Splits off from how we did the outer tests, and is a mess.
                    var res = JSON.parse(results);
                    if (res && !res.status) {
                      if (res.length > 0) {
                        res.forEach(function (sug) {
                          fet.populateAndAddSuggestion(sug);
                        });
                      }
                      // if no suggestions were found, give the a congratulations.
                      fet.analysisSteps.removeClass('animatedEllipsis');
                      fet.analysisSteps.html('Done.');
                      if (fet.container.find('.suggestionTemplate').length === 0) {
                        fet.congratulations.show();
                      }
                    }
                  }
                });

              }
            }
          });

        } else {
          fet.analysisSteps.html('');
          fet.resultsTitle.html('');
          if (res && res.curl_error) {
            fet.errorMsg.html('<span class="formSubmittedMsgAlert">' + res.curl_error + '</span>');
          } else {
            fet.errorMsg.html('<span class="formSubmittedMsgAlert">Error requesting page.</span>');
          }
          fet.loading.fadeOut();
        }
      }
    });
  });

});

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
  suggestion.querySelector('.suggestion-description').innerHTML = sug.description;
  suggestion.removeAttribute('hidden');

  fet.container.append(suggestion);
};
