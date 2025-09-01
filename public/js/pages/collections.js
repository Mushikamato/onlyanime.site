/**
 * Collections JavaScript Handler
 * Manages unified bookmarks and lists interface
 */

"use strict";

var Collections = {
    
    currentTab: 'bookmarks',
    searchTimeout: null,

    /**
     * Initialize collections functionality
     */
    init: function() {
        this.bindEvents();
        this.initCurrentTab();
    },

    /**
     * Bind event listeners
     */
    bindEvents: function() {
        // Tab switching
        $('.collections-tab').on('click', this.handleTabSwitch.bind(this));
        
        // Search functionality
        $('#collections-search-input').on('input', this.handleSearch.bind(this));
        
        // Close search modal on outside click
        $('#collections-search-modal').on('hidden.bs.modal', this.clearSearch.bind(this));
    },

    /**
     * Initialize current tab based on URL or default
     */
    initCurrentTab: function() {
        var activeTab = $('.collections-tab.active').data('tab');
        if (activeTab) {
            this.currentTab = activeTab;
        }
        
        // Initialize infinite scroll for bookmarks tab
        if (this.currentTab === 'bookmarks') {
            this.initBookmarksInfiniteScroll();
        }
    },

    /**
     * Handle tab switching
     */
    handleTabSwitch: function(e) {
        e.preventDefault();
        
        var $tab = $(e.currentTarget);
        var tabName = $tab.data('tab');
        
        if (tabName === this.currentTab) {
            return; // Already active
        }

        // Update active states
        $('.collections-tab').removeClass('active');
        $tab.addClass('active');
        
        // Update content
        this.switchTabContent(tabName);
        
        // Update URL without page refresh
        var url = new URL(window.location);
        url.searchParams.set('tab', tabName);
        window.history.pushState({}, '', url);
        
        this.currentTab = tabName;
    },

    /**
     * Switch tab content via AJAX
     */
    switchTabContent: function(tabName) {
        var $contentArea = $('.collections-content');
        
        // Show loading
        $contentArea.html('<div class="text-center p-4"><div class="spinner-border" role="status"></div></div>');
        
        // Fetch new content
        $.get('/collections', { tab: tabName })
            .done(function(response) {
                // Update content area with new HTML
                $contentArea.html($(response).find('.collections-content').html());
                
                // Reinitialize functionality for new content
                if (tabName === 'bookmarks') {
                    Collections.initBookmarksInfiniteScroll();
                } else if (tabName === 'lists') {
                    // Reinitialize any lists-specific functionality
                    if (typeof Lists !== 'undefined' && Lists.init) {
                        Lists.init();
                    }
                }
            })
            .fail(function() {
                $contentArea.html('<div class="text-center p-4 text-danger">Error loading content</div>');
            });
    },

    /**
     * Initialize infinite scroll for bookmarks
     */
    initBookmarksInfiniteScroll: function() {
        if (typeof PostsPaginator !== 'undefined') {
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
        
        $.get('/collections/search', {
            q: query,
            tab: this.currentTab
        })
        .done(function(response) {
            if (response.success) {
                Collections.displaySearchResults(response.data, response.tab, response.query);
            } else {
                $results.html('<div class="text-center p-2 text-muted">No results found</div>');
            }
        })
        .fail(function() {
            $results.html('<div class="text-center p-2 text-danger">Search error</div>');
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
            html += '<div class="search-results-header mb-2"><small class="text-muted">' + results.length + ' results in ' + tab + '</small></div>';
            
            if (tab === 'bookmarks') {
                // Display bookmark results
                results.forEach(function(post) {
                    html += '<div class="search-result-item p-2 border-bottom">';
                    html += '<div class="d-flex">';
                    html += '<img src="' + post.user.avatar + '" class="rounded-circle mr-2" width="32" height="32">';
                    html += '<div>';
                    html += '<div class="font-weight-bold">' + post.user.username + '</div>';
                    html += '<div class="text-muted small">' + (post.message ? post.message.substring(0, 100) + '...' : 'Post') + '</div>';
                    html += '</div></div></div>';
                });
            } else if (tab === 'lists') {
                // Display list results
                results.forEach(function(list) {
                    html += '<div class="search-result-item p-2 border-bottom">';
                    html += '<div class="d-flex justify-content-between">';
                    html += '<div><div class="font-weight-bold">' + list.name + '</div>';
                    html += '<div class="text-muted small">' + list.members.length + ' members</div></div>';
                    html += '<a href="/lists/' + list.id + '" class="btn btn-sm btn-outline-primary">View</a>';
                    html += '</div></div>';
                });
            }
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