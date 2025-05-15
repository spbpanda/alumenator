var searchToggler = $('.search-toggler'),
  searchInputWrapper = $('.search-input-wrapper'),
  searchInput = $('.search-input'),
  contentBackdrop = $('.content-backdrop');

// Open search input on click of search icon
if (searchToggler.length) {
  searchToggler.on('click', function () {
    if (searchInputWrapper.length) {
      searchInputWrapper.toggleClass('d-none');
      searchInput.focus();
    }
  });
}

window.Helpers.initNavbarDropdownScrollbar();

// Open search on 'CTRL+/'
$(document).on('keydown', function (event) {
  let ctrlKey = event.ctrlKey,
    slashKey = event.which === 191;

  if (ctrlKey && slashKey) {
    if (searchInputWrapper.length) {
      searchInputWrapper.toggleClass('d-none');
      searchInput.focus();
    }
  }
});

searchInput.on('focus', function () {
  if (searchInputWrapper.hasClass('container-xxl')) {
    searchInputWrapper.find('.twitter-typeahead').addClass('container-xxl');
  }
});

if (searchInput.length) {
  // Filter config
  var filterConfig = function () {
    return function findMatches(query, syncResults, asyncResults) {
      $.ajax({
        url: '/admin/search?text='+encodeURIComponent(query.toLowerCase()),
        dataType: 'json',
        async: true,
      }).done(function(searchData){
        return asyncResults(searchData);
      });
    };
  };

  // Init typeahead on searchInput
  searchInput.each(function () {
    var $this = $(this);
    searchInput
      .typeahead(
        {
          hint: false,
          classNames: {
            menu: 'tt-menu navbar-search-suggestion',
            cursor: 'active',
            suggestion: 'suggestion d-flex justify-content-between px-3 py-2 w-100'
          }
        },
        {
          name: 'results',
          display: 'name',
          limit: 50,
          source: filterConfig(),
          'async': true,
          templates: {
            // header: '<h6 class="suggestions-header text-primary mb-0 mx-3 mt-3 pb-2">Pages</h6>',
            suggestion: function ({ url, name, icon }) {
            return (
                '<a href="' + url + '">' +
                '<div>' +
                (icon.indexOf('IMG:') === 0 ?
                  '<img style="max-height: 24px; max-width: 24px" src="' + icon.substring(4).slice(0, -1) + '">' // img by url
                  : icon === '!AVA!' ?
                    '<img style="max-height: 24px; max-width: 24px" src="https://mc-heads.net/avatar/' + name + '/24">' // img by avatar
                      : '<i class="' + icon + ' me-2"></i>' //icon
                ) +
                '<span class="align-middle">' + name + '</span>' +
                '</div>' +
                '</a>'
              );
            },
            notFound:
              '<div class="not-found px-3 py-2">' +
              // '<h6 class="suggestions-header text-primary mb-2">Pages</h6>' +
              '<p class="py-2 mb-0"><i class="bx bx-error-circle bx-xs me-2"></i> No Results Found</p>' +
              '</div>'
          }
        },
      )
      //On typeahead result render.
      .bind('typeahead:render', function () {
        // Show content backdrop,
        contentBackdrop.addClass('show').removeClass('fade');
      })
      // On typeahead select
      .bind('typeahead:select', function (ev, suggestion) {
        // Open selected page
        if (suggestion.url !== 'javascript:;') {
            window.location.href = suggestion.url;
        }
      })
      // On typeahead close
      .bind('typeahead:close', function () {
        // Clear search
        searchInput.val('');
        $this.typeahead('val', '');
        // Hide search input wrapper
        searchInputWrapper.addClass('d-none');
        // Fade content backdrop
        contentBackdrop.addClass('fade').removeClass('show');
      });

    // On searchInput keyup, Fade content backdrop if search input is blank
    searchInput.on('keyup', function () {
      if (searchInput.val() == '') {
        contentBackdrop.addClass('fade').removeClass('show');
      }
    });
  });
    // Init PerfectScrollbar in search result
    var psSearch;
    $('.navbar-search-suggestion').each(function () {
        psSearch = new PerfectScrollbar($(this)[0], {
            wheelPropagation: false,
            suppressScrollX: true
        });
    });

    searchInput.on('keyup', function () {
        psSearch.update();
    });
}
