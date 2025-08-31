/*
* Hockey Training Modal Management
* Handle drill assignment to training sessions
*/

var CCC = CCC || {};

(function($){
  CCC.trainingModal = {
    
    currentPostId: null,
    modal: null,
    
    init: function() {
      this.bindEvents();
      this.modifyFavoriteButtons();
    },
    
    bindEvents: function() {
      // Handle favorite button clicks (works alongside select.js for logged-in users)
      $(document).on('click', '.ccc-favorite-post-toggle-button', this.handleFavoriteClick.bind(this));
      
      // Modal events
      $(document).on('click', '.ccc-select-training', this.selectTraining.bind(this));
      $(document).on('click', '.ccc-create-new-training', this.showCreateForm.bind(this));
      $(document).on('click', '.ccc-save-new-training', this.saveNewTraining.bind(this));
      $(document).on('click', '.ccc-modal-close, .ccc-modal-overlay', this.closeModal.bind(this));
      $(document).on('click', '.ccc-modal-content', function(e) { e.stopPropagation(); });
      
      // Gallery events
      $(document).on('click', '.ccc-remove-from-training', this.removeFromTraining.bind(this));
      $(document).on('click', '.ccc-assign-to-training', this.showAssignModal.bind(this));
    },
    
    modifyFavoriteButtons: function() {
      // Check which trainings this drill is already in
      $('.ccc-favorite-post-toggle-button').each(function() {
        var postId = $(this).data('post_id-ccc_favorite');
        CCC.trainingModal.checkDrillStatus(postId, $(this));
      });
    },
    
    checkDrillStatus: function(postId, button) {
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: 'ccc_get_drill_trainings',
          nonce: CCC_MY_TRAINING.get_drill_nonce,
          post_id: postId
        },
        success: function(response) {
          if (response.success && response.data.training_ids.length > 0) {
            button.addClass('save');
            button.attr('title', 'In ' + response.data.training_ids.length + ' training(s)');
          }
        }
      });
    },
    
    handleFavoriteClick: function(e) {
      // Only handle if user is logged in, otherwise let select.js handle it
      if (!CCC_MY_FAVORITE_UPDATE.user_logged_in) {
        // Don't prevent default - let the original handler work
        return;
      }
      
      e.preventDefault();
      e.stopImmediatePropagation(); // Stop other handlers from executing
      
      this.currentPostId = $(e.currentTarget).data('post_id-ccc_favorite');
      this.showModal();
    },
    
    showModal: function() {
      var self = this;
      
      // Load training sessions
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.get_action,
          nonce: CCC_MY_TRAINING.get_nonce
        },
        success: function(response) {
          if (response.success) {
            self.createModal(response.data);
          }
        }
      });
    },
    
    createModal: function(data) {
      var sessions = data.sessions || [];
      
      var modalHtml = '<div class="ccc-modal-overlay">' +
        '<div class="ccc-modal-content">' +
        '<div class="ccc-modal-header">' +
        '<h2>Add Drill to Training Session</h2>' +
        '<button class="ccc-modal-close">×</button>' +
        '</div>' +
        '<div class="ccc-modal-body">';
      
      // Check current assignments
      var currentTrainings = [];
      sessions.forEach(function(session) {
        if (session.drills && session.drills.indexOf(CCC.trainingModal.currentPostId) !== -1) {
          currentTrainings.push(session);
        }
      });
      
      if (currentTrainings.length > 0) {
        modalHtml += '<div class="ccc-current-trainings">' +
          '<h3>Currently in:</h3>' +
          '<ul>';
        currentTrainings.forEach(function(training) {
          modalHtml += '<li>' + training.name + ' (' + training.date + ')' +
            '<button class="ccc-remove-btn" data-training-id="' + training.id + '">Remove</button></li>';
        });
        modalHtml += '</ul></div>';
      }
      
      modalHtml += '<div class="ccc-training-list">' +
        '<h3 style="font-size: 18px !important; margin: 0 0 12px 0 !important; font-weight: 600 !important;">Select Training Session:</h3>';
      
      if (sessions.length > 0) {
        modalHtml += '<div class="ccc-training-grid">';
        sessions.forEach(function(session) {
          var isSelected = currentTrainings.some(t => t.id === session.id);
          modalHtml += '<div class="ccc-training-card' + (isSelected ? ' selected' : '') + '">' +
            '<h4>' + session.name + '</h4>' +
            '<p class="ccc-training-date">' + session.date + '</p>' +
            '<p class="ccc-drill-count">' + session.drill_count + ' drills</p>' +
            '<button class="ccc-select-training" data-training-id="' + session.id + '">' +
            (isSelected ? 'Already Added' : 'Add to This') +
            '</button>' +
            '</div>';
        });
        modalHtml += '</div>';
      } else {
        modalHtml += '<p>No training sessions yet.</p>';
      }
      
      modalHtml += '</div>' +
        '<div class="ccc-new-training-section">' +
        '<button class="ccc-create-new-training">Create New Training Session</button>' +
        '<div class="ccc-new-training-form" style="display:none;">' +
        '<input type="text" id="ccc-new-training-name" placeholder="Training name">' +
        '<input type="date" id="ccc-new-training-date" value="' + new Date().toISOString().split('T')[0] + '">' +
        '<button class="ccc-save-new-training">Save & Add Drill</button>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>' +
        '</div>';
      
      $('body').append(modalHtml);
      this.modal = $('.ccc-modal-overlay');
      
      // Bind remove buttons
      $('.ccc-remove-btn').on('click', function(e) {
        e.preventDefault();
        var trainingId = $(this).data('training-id');
        CCC.trainingModal.removeFromTraining(CCC.trainingModal.currentPostId, trainingId);
      });
    },
    
    selectTraining: function(e) {
      e.preventDefault();
      var trainingId = $(e.currentTarget).data('training-id');
      
      if ($(e.currentTarget).text() === 'Already Added') {
        return;
      }
      
      this.addToTraining(this.currentPostId, trainingId);
    },
    
    showCreateForm: function(e) {
      e.preventDefault();
      $('.ccc-new-training-form').slideToggle();
    },
    
    saveNewTraining: function(e) {
      e.preventDefault();
      
      var name = $('#ccc-new-training-name').val();
      var date = $('#ccc-new-training-date').val();
      
      if (!name) {
        alert('Please enter a training name.');
        return;
      }
      
      var self = this;
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.save_action,
          nonce: CCC_MY_TRAINING.save_nonce,
          session_name: name,
          session_date: date
        },
        success: function(response) {
          if (response.success) {
            // Add drill to new training
            self.addToTraining(self.currentPostId, response.data.id);
          }
        }
      });
    },
    
    addToTraining: function(postId, trainingId) {
      var self = this;
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: 'ccc_add_drill_to_training',
          nonce: CCC_MY_TRAINING.add_drill_nonce,
          post_id: postId,
          training_id: trainingId
        },
        success: function(response) {
          if (response.success) {
            self.closeModal();
            // Update button
            var button = $('.ccc-favorite-post-toggle-button[data-post_id-ccc_favorite="' + postId + '"]');
            button.addClass('save');
            self.checkDrillStatus(postId, button);
            
            // Reload if on gallery page
            if ($('#ccc-training-gallery').length) {
              CCC.trainingGallery.loadGallery();
            }
          }
        }
      });
    },
    
    removeFromTraining: function(postId, trainingId) {
      // Handle both direct calls and event calls
      if (typeof postId === 'object') {
        var e = postId;
        e.preventDefault();
        var postId = $(e.currentTarget).data('post-id');
        var trainingId = $(e.currentTarget).data('training-id');
      }
      
      var self = this;
      
      if (!confirm('Remove this drill from the training session?')) {
        return;
      }
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: 'ccc_remove_drill_from_training',
          nonce: CCC_MY_TRAINING.remove_drill_nonce,
          post_id: postId,
          training_id: trainingId
        },
        success: function(response) {
          if (response.success) {
            // Update UI
            if (self.modal) {
              self.closeModal();
              self.showModal(); // Refresh modal
            }
            
            // Update button
            var button = $('.ccc-favorite-post-toggle-button[data-post_id-ccc_favorite="' + postId + '"]');
            self.checkDrillStatus(postId, button);
            
            // Reload gallery if present
            if ($('#ccc-training-gallery').length) {
              CCC.trainingGallery.loadGallery();
            }
          }
        }
      });
    },
    
    showAssignModal: function(e) {
      e.preventDefault();
      this.currentPostId = $(e.currentTarget).data('post-id');
      this.showModal();
    },
    
    closeModal: function() {
      if (this.modal) {
        this.modal.remove();
        this.modal = null;
      }
    }
  };
  
  // Initialize when document is ready
  $(document).ready(function() {
    if (CCC_MY_TRAINING && CCC_MY_TRAINING.api) {
      CCC.trainingModal.init();
    }
  });
  
})(jQuery);