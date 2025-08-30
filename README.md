# Hockey Training Favorites Plugin

This enhanced version of the My Favorites plugin has been specifically developed for hockey coaches to create and manage training sessions.

## How It Works

### For Coaches:

1. **Adding Drills to Training Sessions**:
   - On any drill page, click the "Favorite" button
   - A modal will appear showing all your training sessions
   - Select an existing training session OR create a new one
   - The drill is automatically added to that training session

2. **Creating New Training Sessions**:
   - When clicking "Favorite" on a drill, you can create a new training session
   - Enter a name and date for the training
   - The drill is automatically added to the new session

3. **Managing Training Sessions**:
   - View all your training sessions in a modern gallery layout
   - Each training shows its drills as thumbnails
   - Search and filter training sessions by name or date
   - Remove drills from training sessions
   - Delete entire training sessions

## Key Features

### ✅ Modal-Based Drill Assignment
- Click any "Favorite" button to see assignment modal
- Shows current training sessions for that drill
- Create new training sessions on-the-fly
- Works on every drill post page

### ✅ Training Gallery View
- Modern card-based layout inspired by Field Hockey Platform design
- Groups drills by training session
- Shows "Unassigned Drills" for drills not in any training
- Expandable drill thumbnails for each training

### ✅ Smart Organization
- Drills without training assignments are grouped as "Unassigned"
- Easy to assign unassigned drills to training sessions
- Remove drills from training sessions with one click
- Delete entire training sessions

### ✅ Modern Styling
- Based on Field Hockey Platform design (html.html)
- Orange (#F77F00) and blue (#4169E1) color scheme
- Responsive design for all devices
- Unique CSS classes (ccc-mf-*, ccc-training-*) to avoid conflicts

## Shortcodes

### Original shortcodes (work as before):
```
[ccc_my_favorite_select_button] - Shows modal to assign drill to training
[ccc_my_favorite_list_menu] - Shows favorite count badge  
[ccc_my_favorite_list_results] - Shows all favorite drills
```

### New shortcodes:
```
[ccc_my_training_gallery] - Shows training sessions gallery with drills
```

## Shortcode Parameters

### [ccc_my_favorite_select_button]
- `post_id` - Specific post ID (optional, uses current post if not specified)
- `text` - Button text (default: "Favorite")
- `style` - Style variant (default: "1")

### [ccc_my_favorite_list_results]
- `class` - Additional CSS classes
- `style` - Style variant (default: "1") 
- `posts_per_page` - Number of posts to show (default: "100")
- `excerpt` - Excerpt character length (default: "0" - no excerpt)

### [ccc_my_training_gallery]
- `title` - Gallery title (default: "My Training Sessions")
- `show_search` - Show search bar (default: "true")
- `show_filter` - Show filter dropdown (default: "true")
- `class` - Additional CSS classes

## Example Usage

### On Drill Pages:
```html
[ccc_my_favorite_select_button text="Add to Training"]
```

### Favorites Overview Page:
```html
[ccc_my_favorite_list_results posts_per_page="20" excerpt="100"]
```

### Training Gallery Page:
```html
[ccc_my_training_gallery title="My Hockey Training Sessions" show_search="true" show_filter="true"]
```

## Database Structure

The plugin uses WordPress user meta to store:
- `ccc_my_favorite_post_ids` - Legacy favorite posts (for backward compatibility)
- `ccc_my_training_sessions` - Training session metadata (name, date, id)
- `ccc_my_training_drills` - Drill-to-training relationships

## Technical Features

- **Backward Compatible**: All existing shortcodes continue to work
- **User-Friendly**: Modal-based interaction for easy drill assignment
- **Responsive**: Works on all devices and screen sizes
- **Modern Design**: Based on Field Hockey Platform styling
- **Conflict-Free**: Unique CSS classes prevent styling conflicts

## CSS Classes Used

To avoid conflicts with existing site CSS, the plugin uses prefixed classes:
- `.ccc-mf-*` - Modern favorite buttons
- `.ccc-training-*` - Training session elements
- `.ccc-modal-*` - Modal dialog elements
- `.ccc-gallery-*` - Gallery layout elements

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin via the WordPress admin panel
3. Add shortcodes to your pages:
   - Add `[ccc_my_favorite_select_button]` to drill pages
   - Add `[ccc_my_training_gallery]` to create a training sessions page

## Support

For questions or issues, please contact the website administrator.