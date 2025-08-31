/*
* Hockey Training Gallery
* Display training sessions with drills in a card-based gallery
*/

var CCC = CCC || {};

(function($){
  CCC.trainingGallery = {
    
    allSessions: [],
    allUnassigned: [],
    
    init: function() {
      this.bindEvents();
      this.loadGallery();
    },
    
    bindEvents: function() {
      // Filter and search
      $(document).on('input', '#ccc-training-search', this.filterGallery.bind(this));
      $(document).on('change', '#ccc-training-filter', this.filterGallery.bind(this));
      
      // Training actions
      $(document).on('click', '.ccc-delete-training', this.deleteTraining.bind(this));
      $(document).on('click', '.ccc-view-training-drills', this.toggleDrills.bind(this));
    },
    
    loadGallery: function() {
      var self = this;
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.get_action,
          nonce: CCC_MY_TRAINING.get_nonce
        },
        success: function(response) {
          if (response.success) {
            self.allSessions = response.data.sessions || [];
            self.allUnassigned = response.data.unassigned || [];
            self.displayGallery();
          }
        }
      });
    },
    
    displayGallery: function() {
      var container = $('#ccc-training-gallery');
      if (container.length === 0) return;
      
      var html = '';
      
      // Show unassigned drills first if any
      if (this.allUnassigned.length > 0) {
        html += this.createUnassignedSection();
      }
      
      // Show training sessions
      if (this.allSessions.length > 0) {
        this.allSessions.forEach(function(session) {
          html += CCC.trainingGallery.createTrainingCard(session);
        });
      } else if (this.allUnassigned.length === 0) {
        html += '<div class="ccc-no-trainings">No training sessions created yet.</div>';
      }
      
      container.html(html);
      
      // Load drill details for each card
      this.loadDrillDetails();
    },
    
    createUnassignedSection: function() {
      return '<div class="ccc-training-card ccc-unassigned-drills">' +
        '<div class="ccc-training-header">' +
        '<h3>Unassigned Drills</h3>' +
        '<span class="ccc-drill-count">' + this.allUnassigned.length + ' drills</span>' +
        '</div>' +
        '<div class="ccc-drills-preview" data-training-id="none">' +
        '<p>Drills not assigned to any training session</p>' +
        '</div>' +
        '<div class="ccc-training-actions">' +
        '<button class="ccc-view-training-drills" data-training-id="none">View Drills</button>' +
        '</div>' +
        '</div>';
    },
    
    createTrainingCard: function(session) {
      return '<div class="ccc-training-card" data-training-id="' + session.id + '">' +
        '<div class="ccc-training-header">' +
        '<h3>' + session.name + '</h3>' +
        '<button class="ccc-delete-training" data-training-id="' + session.id + '">×</button>' +
        '</div>' +
        '<div class="ccc-training-meta">' +
        '<span class="ccc-training-date">' + session.date + '</span>' +
        '<span class="ccc-drill-count">' + session.drill_count + ' drills</span>' +
        '</div>' +
        '<div class="ccc-drills-preview" data-training-id="' + session.id + '">' +
        '<p>Loading drills...</p>' +
        '</div>' +
        '<div class="ccc-training-actions">' +
        '<button class="ccc-view-training-drills" data-training-id="' + session.id + '">View Drills</button>' +
        '</div>' +
        '</div>';
    },
    
    loadDrillDetails: function() {
      var self = this;
      
      // Load drills for each training
      this.allSessions.forEach(function(session) {
        if (session.drills && session.drills.length > 0) {
          self.loadDrillsForTraining(session.id, session.drills);
        }
      });
      
      // Load unassigned drills
      if (this.allUnassigned.length > 0) {
        this.loadDrillsForTraining('none', this.allUnassigned);
      }
    },
    
    loadDrillsForTraining: function(trainingId, drillIds) {
      if (!drillIds || drillIds.length === 0) {
        $('.ccc-drills-preview[data-training-id="' + trainingId + '"]').html('<p>No drills assigned</p>');
        return;
      }
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: 'get_posts_by_ids',
          post_ids: drillIds.join(','),
          nonce: CCC_MY_TRAINING.get_posts_nonce
        },
        success: function(response) {
          if (response.success && response.data) {
            var html = '<div class="ccc-drill-thumbnails" style="display:none;">';
            
            response.data.forEach(function(post) {
              html += '<div class="ccc-drill-thumb" data-post-id="' + post.id + '">' +
                '<a href="' + post.permalink + '">' +
                '<img src="' + post.thumbnail + '" alt="' + post.title + '" />' +
                '</a>' +
                '<h5><a href="' + post.permalink + '">' + post.title + '</a></h5>' +
                '<div class="ccc-drill-actions">' +
                '<button class="ccc-remove-from-training" data-post-id="' + post.id + '" data-training-id="' + trainingId + '">Remove</button>';
                
              if (trainingId === 'none') {
                html += '<button class="ccc-assign-to-training" data-post-id="' + post.id + '">Assign</button>';
              }
              
              html += '</div></div>';
            });
            
            html += '</div>' +
              '<p class="ccc-drills-summary">' + drillIds.length + ' drills in this training</p>';
            
            $('.ccc-drills-preview[data-training-id="' + trainingId + '"]').html(html);
          } else {
            $('.ccc-drills-preview[data-training-id="' + trainingId + '"]').html('<p>No drills found</p>');
          }
        }
      });
    },
    
    toggleDrills: function(e) {
      e.preventDefault();
      var trainingId = $(e.currentTarget).data('training-id');
      var drillsContainer = $('.ccc-drills-preview[data-training-id="' + trainingId + '"] .ccc-drill-thumbnails');
      
      drillsContainer.slideToggle();
      
      var button = $(e.currentTarget);
      button.text(button.text() === 'View Drills' ? 'Hide Drills' : 'View Drills');
    },
    
    deleteTraining: function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      if (!confirm('Are you sure you want to delete this training session? All drills will become unassigned.')) {
        return;
      }
      
      var trainingId = $(e.currentTarget).data('training-id');
      var self = this;
      
      $.ajax({
        url: CCC_MY_TRAINING.api,
        type: 'POST',
        data: {
          action: CCC_MY_TRAINING.delete_action,
          nonce: CCC_MY_TRAINING.delete_nonce,
          session_id: trainingId
        },
        success: function(response) {
          if (response.success) {
            self.loadGallery();
          }
        }
      });
    },
    
    filterGallery: function() {
      var searchTerm = $('#ccc-training-search').val().toLowerCase();
      var filterValue = $('#ccc-training-filter').val();
      
      $('.ccc-training-card').each(function() {
        var card = $(this);
        var title = card.find('h3').text().toLowerCase();
        var date = card.find('.ccc-training-date').text();
        
        var matchesSearch = title.indexOf(searchTerm) > -1;
        var matchesFilter = true;
        
        if (filterValue === 'week') {
          var cardDate = new Date(date);
          var today = new Date();
          var weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
          matchesFilter = cardDate >= weekAgo && cardDate <= today;
        } else if (filterValue === 'month') {
          var cardDate = new Date(date);
          var today = new Date();
          var monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
          matchesFilter = cardDate >= monthAgo && cardDate <= today;
        }
        
        card.toggle(matchesSearch && matchesFilter);
      });
    }
  };
  
  // Initialize when document is ready
  $(document).ready(function() {
    if ($('#ccc-training-gallery').length && CCC_MY_TRAINING && CCC_MY_TRAINING.api) {
      CCC.trainingGallery.init();
    }
  });
  
})(jQuery);