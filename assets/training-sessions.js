/*
* Hockey Training Sessions Management
* Manage training sessions with date/week grouping
*/

var CCC = CCC || {};

(function($){
  CCC.training = {
    
    init: function() {
      this.bindEvents();
      this.loadSessions();
    },
    
    bindEvents: function() {
      // Save current favorites as training session
      $(document).on('click', '.ccc-save-training-session', this.saveSession.bind(this));
      
      // Load a saved session
      $(document).on('click', '.ccc-load-session', this.loadSession.bind(this));
      
      // Delete a session
      $(document).on('click', '.ccc-delete-session', this.deleteSession.bind(this));
      
      // Filter sessions
      $(document).on('change', '#ccc-session-filter', this.filterSessions.bind(this));
      $(document).on('input', '#ccc-session-search', this.searchSessions.bind(this));
    },
    
    saveSession: function(e) {
      e.preventDefault();
      
      // Get current favorites
      var favorites = this.getCurrentFavorites();
      if (!favorites || favorites.length === 0) {
        alert('Please select some drills as favorites before saving a training session.');
        return;
      }
      
      // Show save dialog
      var dialog = this.createSaveDialog();
      $('body').append(dialog);
      
      // Handle save
      $('#ccc-confirm-save-session').on('click', function() {
        var sessionName = $('#ccc-session-name').val();
        var sessionDate = $('#ccc-session-date').val();
        var sessionWeek = $('#ccc-session-week').val();
        
        if (!sessionName) {
          alert('Please enter a name for the training session.');
          return;
        }
        
        if (!sessionDate && !sessionWeek) {
          alert('Please select a date or week for the training session.');
          return;
        }
        
        $.ajax({
          url: CCC_MY_TRAINING.api,
          type: 'POST',
          data: {
            action: CCC_MY_TRAINING.save_action,
            nonce: CCC_MY_TRAINING.save_nonce,
            session_name: sessionName,
            session_date: sessionDate,
            session_week: sessionWeek,
            post_ids: favorites
          },
          success: function(response) {
            if (response.success) {
              $('#ccc-training-dialog').remove();
              CCC.training.loadSessions();
              alert('Training session saved successfully!');
            } else {
              alert('Something went wrong while saving.');
            }
          }
        });
      });
      
      // Handle cancel
      $('#ccc-cancel-save-session, .ccc-dialog-close').on('click', function() {
        $('#ccc-training-dialog').remove();
      });
    },
    
    loadSessions: function() {
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.get_action,
          nonce: CCC_MY_TRAINING.get_nonce
        },
        success: function(response) {
          if (response.success && response.data) {
            CCC.training.displaySessions(response.data);
          }
        }
      });
    },
    
    displaySessions: function(sessions) {
      var container = $('#ccc-training-sessions-list');
      if (container.length === 0) return;
      
      if (!sessions || sessions.length === 0) {
        container.html('<div class="ccc-no-sessions">No training sessions saved yet.</div>');
        return;
      }
      
      // Group sessions by week/date
      var grouped = this.groupSessions(sessions);
      var html = '';
      
      Object.keys(grouped).sort().reverse().forEach(function(group) {
        html += '<div class="ccc-session-group">';
        html += '<h3 class="ccc-session-group-title">' + group + '</h3>';
        html += '<div class="ccc-session-cards">';
        
        grouped[group].forEach(function(session) {
          html += CCC.training.createSessionCard(session);
        });
        
        html += '</div></div>';
      });
      
      container.html(html);
    },
    
    groupSessions: function(sessions) {
      var grouped = {};
      
      sessions.forEach(function(session) {
        var groupKey = session.week ? 'Week ' + session.week : session.date;
        if (!grouped[groupKey]) {
          grouped[groupKey] = [];
        }
        grouped[groupKey].push(session);
      });
      
      return grouped;
    },
    
    createSessionCard: function(session) {
      var postCount = session.post_ids ? session.post_ids.split(',').length : 0;
      var dateInfo = session.week ? 'Week ' + session.week : session.date;
      
      return '<div class="ccc-session-card" data-session-id="' + session.id + '">' +
        '<div class="ccc-session-card-header">' +
        '<h4 class="ccc-session-title">' + session.name + '</h4>' +
        '<button class="ccc-delete-session" data-id="' + session.id + '">×</button>' +
        '</div>' +
        '<div class="ccc-session-meta">' +
        '<span class="ccc-session-date">' + dateInfo + '</span>' +
        '<span class="ccc-session-count">' + postCount + ' drills</span>' +
        '</div>' +
        '<div class="ccc-session-actions">' +
        '<button class="ccc-load-session ccc-btn-primary" data-id="' + session.id + '" data-posts="' + session.post_ids + '">Load training</button>' +
        '</div>' +
        '</div>';
    },
    
    loadSession: function(e) {
      e.preventDefault();
      var postIds = $(e.target).data('posts');
      
      if (!postIds) {
        alert('This training session contains no drills.');
        return;
      }
      
      // Clear current favorites and load session
      if (CCC_MY_FAVORITE_UPDATE.user_logged_in == false) {
        localStorage.setItem('ccc-my_favorite_post', postIds);
        location.reload();
      } else {
        $.ajax({
          url: CCC_MY_FAVORITE_UPDATE.api,
          type: 'POST',
          data: {
            action: CCC_MY_FAVORITE_UPDATE.action,
            nonce: CCC_MY_FAVORITE_UPDATE.nonce,
            post_ids: postIds
          },
          success: function() {
            location.reload();
          }
        });
      }
    },
    
    deleteSession: function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      if (!confirm('Are you sure you want to delete this training session?')) {
        return;
      }
      
      var sessionId = $(e.target).data('id');
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.delete_action,
          nonce: CCC_MY_TRAINING.delete_nonce,
          session_id: sessionId
        },
        success: function(response) {
          if (response.success) {
            CCC.training.loadSessions();
          }
        }
      });
    },
    
    filterSessions: function(e) {
      var filterValue = $(e.target).val();
      // Implement filtering logic
    },
    
    searchSessions: function(e) {
      var searchTerm = $(e.target).val().toLowerCase();
      $('.ccc-session-card').each(function() {
        var title = $(this).find('.ccc-session-title').text().toLowerCase();
        $(this).toggle(title.indexOf(searchTerm) > -1);
      });
    },
    
    getCurrentFavorites: function() {
      if (CCC_MY_FAVORITE_UPDATE.user_logged_in == false) {
        return localStorage.getItem('ccc-my_favorite_post');
      } else {
        // For logged in users, we need to get from server
        // This would need to be synchronous or handled differently
        return $('.ccc-favorite-post-toggle-button.save').map(function() {
          return $(this).data('post_id-ccc_favorite');
        }).get().join(',');
      }
    },
    
    createSaveDialog: function() {
      var today = new Date().toISOString().split('T')[0];
      var weekNum = this.getWeekNumber(new Date());
      
      return '<div id="ccc-training-dialog" class="ccc-dialog-overlay">' +
        '<div class="ccc-dialog">' +
        '<div class="ccc-dialog-header">' +
        '<h2>Save Training Session</h2>' +
        '<button class="ccc-dialog-close">×</button>' +
        '</div>' +
        '<div class="ccc-dialog-body">' +
        '<div class="ccc-form-group">' +
        '<label for="ccc-session-name">Training session name:</label>' +
        '<input type="text" id="ccc-session-name" placeholder="E.g. Warm-up Week 12" />' +
        '</div>' +
        '<div class="ccc-form-group">' +
        '<label for="ccc-session-date">Date:</label>' +
        '<input type="date" id="ccc-session-date" value="' + today + '" />' +
        '</div>' +
        '<div class="ccc-form-group">' +
        '<label for="ccc-session-week">Or select week:</label>' +
        '<input type="number" id="ccc-session-week" min="1" max="52" placeholder="Week number (1-52)" value="' + weekNum + '" />' +
        '</div>' +
        '</div>' +
        '<div class="ccc-dialog-footer">' +
        '<button id="ccc-cancel-save-session" class="ccc-btn-secondary">Cancel</button>' +
        '<button id="ccc-confirm-save-session" class="ccc-btn-primary">Save</button>' +
        '</div>' +
        '</div>' +
        '</div>';
    },
    
    getWeekNumber: function(d) {
      d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
      d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
      var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
      var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
      return weekNo;
    }
  };
  
  $(document).ready(function() {
    if (CCC_MY_TRAINING && CCC_MY_TRAINING.api) {
      CCC.training.init();
    }
  });
  
})(jQuery);