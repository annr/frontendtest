var fet = {
  title: 'FrontendTest',
  container: $('#results'),
  suggestionTemplate: document.querySelector('.suggestionTemplate'),
  errorMsg: $('#errorMessage'),
  loading: $('.loading'),
};

 $(document).ready(function() {
  $( "#testFrontend" ).submit(function( event ) {
    event.preventDefault();

    fet.container.html('');
    fet.errorMsg.html('');
    fet.loading.show();
    
    $.ajax({
      type: "POST",
      url: "src/php/requestPage.php?url=" + $('#url').val(),
      data: $("#testFrontend").serialize(),
      error: function(err) {
        fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
        fet.loading.hide();
      },
      success: function(data) {
        var res = JSON.parse(data);
          // we only include 'status' if something goes wrong.
        if(res && !res.status) {
          if (res.length > 0) {
            res.forEach(function(sug) {
              fet.populateAndAddSuggestion(sug);
            });
          }

          $.ajax({
            type: "POST",
            url: "src/php/analyzeHTMLHead.php?url=" + $('#url').val(),
            error: function(err) {
              fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
              fet.loading.hide();
            },
            success: function(results) {
              // Splits off from how we did the outer tests, and is a mess.
              var res = JSON.parse(results);
              if(res && !res.status) {
                if (res.length > 0) {
                  res.forEach(function(sug) {
                    fet.populateAndAddSuggestion(sug);
                  });
                }
              }
            }
          });

          $.ajax({
            type: "POST",
            url: "src/php/analyzeHTMLBody.php?url=" + $('#url').val(),
            error: function(err) { 
              fet.errorMsg.html('<span class="formSubmittedMsgAlert">Sorry. An error occurred.</span>');
              fet.loading.hide();
            },
            success: function(results) {
              // Splits off from how we did the outer tests, and is a mess.
              var res = JSON.parse(results);
              if(res && !res.status) {
                if (res.length > 0) {
                  res.forEach(function(sug) {
                    fet.populateAndAddSuggestion(sug);
                  });
                }
                // we are going to assume processing the body with PHP is going to take longer than anything.
                // so after these suggestions are processed, well hide the spinner.
                fet.loading.fadeOut();
              }
            }
          });
              
        } else {
          if(res && res.curl_error) {
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
 
fet.populateAndAddSuggestion = function(sug) {
  // bad mix of jQuery and raw
  var suggestion = fet.suggestionTemplate.cloneNode(true);
  suggestion.querySelector('.suggestion-title').textContent = sug.title;
  suggestion.querySelector('.suggestion-description').innerHTML = sug.description;
  suggestion.removeAttribute('hidden');

  fet.container.append(suggestion);
};
