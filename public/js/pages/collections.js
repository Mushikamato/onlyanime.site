/**
 * Collections JavaScript Handler
 * Simple implementation - no AJAX, just search functionality
 */

"use strict";

var Collections = {
    
    searchTimeout: null,

    /**
     * Initialize collections functionality
     */
    init: function() {
        this.bindEvents();
        this.initBookmarksIfNeeded();
    },

    /**
     * Bind event listeners
     */
    bindEvents: function() {
        // Search functionality only
        $('#collections-search-input').on('input', this.handleSearch.bind(this));
        $('#collections-search-modal').on('hidden.bs.modal', this.clearSearch.bind(this));
    },

    /**
     * Initialize bookmarks infinite scroll if on bookmarks tab
     */
    initBookmarksIfNeeded: function() {
        // Only initialize if we're on bookmarks tab and PostsPaginator exists
        if ($('.tab-content.active').attr('id') === 'bookmarks-tab-content' && typeof PostsPaginator !== 'undefined') {
            PostsPaginator.init();
        }
    },

    /**
     * Show search dialog
     */
    showSearchDialog: function() {
        $('#collections-search-modal').modal('show');
        setTimeout(function() {
            $('#collections-search-input').focus();
        }, 500);
    },

    /**
     * Handle search input
     */
    handleSearch: function(e) {
        var query = $(e.target).val().trim();
        
        // Clear previous timeout
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Debounce search
        this.searchTimeout = setTimeout(function() {
            Collections.performSearch(query);
        }, 300);
    },

    /**
     * Perform search request
     */
    performSearch: function(query) {
        var $results = $('#collections-search-results');
        
        if (query.length < 2) {
            $results.html('');
            return;
        }
        
        // Show loading
        $results.html('<div class="text-center p-2"><div class="spinner-border spinner-border-sm"></div></div>');
        
        // Search functionality
        $.get('/my/collections/search', {
            q: query,
            tab: 'bookmarks' // Default to bookmarks for now
        })
        .done(function(response) {
            if (response.success) {
                Collections.displaySearchResults(response.data, response.tab, response.query);
            } else {
                $results.html('<div class="text-center p-2 text-muted">No results found</div>');
            }
        })
        .fail(function() {
            $results.html('<div class="text-center p-2 text-muted">Search functionality coming soon</div>');
        });
    },

    /**
     * Display search results
     */
    displaySearchResults: function(results, tab, query) {
        var $results = $('#collections-search-results');
        var html = '';
        
        if (results.length === 0) {
            html = '<div class="text-center p-2 text-muted">No results found for "' + query + '"</div>';
        } else {
            html += '<div class="search-results-header mb-2"><small class="text-muted">' + results.length + ' results</small></div>';
            
            results.forEach(function(item) {
                html += '<div class="search-result-item p-2 border-bottom">';
                html += '<div class="font-weight-bold">' + (item.name || item.username || 'Result') + '</div>';
                html += '</div>';
            });
        }
        
        $results.html(html);
    },

    /**
     * Clear search
     */
    clearSearch: function() {
        $('#collections-search-input').val('');
        $('#collections-search-results').html('');
    }
};

// Initialize when document is ready
$(document).ready(function() {
    Collections.init();
});