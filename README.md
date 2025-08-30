# Hockey Training Favorites Plugin

This enhanced version of the My Favorites plugin has been specifically developed for hockey coaches to create and manage training sessions.

## New Features

### 1. Save Training Sessions
Coaches can now save their selected favorite drills as a complete training session with:
- A name for the training session
- A date or week number
- All selected drills

### 2. Group Training Sessions
Training sessions are automatically grouped by:
- Week number
- Date
This makes it easy to find training sessions for specific periods.

### 3. Search and Filter
- **Search function**: Search training sessions by name
- **Filter options**: Filter by week, month, or custom period

### 4. Modern Styling
The plugin now uses modern CSS styling based on the Field Hockey Platform design with:
- Modern card-based layouts
- Responsive design for all devices
- Professional colors (orange #F77F00 and blue #4169E1)

## Shortcodes

### Existing shortcodes (remain functional):
```
[ccc_my_favorite_select_button] - Favorite button for a drill
[ccc_my_favorite_list_menu] - Menu showing number of favorites
[ccc_my_favorite_list_results] - List of favorite drills
```

### New shortcodes:
```
[ccc_my_training_save_button] - Button to save current selection as training session
[ccc_my_training_sessions_list] - Overview of all saved training sessions
```

## Usage

### For coaches:

1. **Select drills**: 
   - Browse through the drills on the website
   - Click "Favorite" for each drill you want to add

2. **Save training session**:
   - Go to the favorites overview
   - Click "Save Training Session"
   - Give the training session a name
   - Select a date or week number
   - Click "Save"

3. **Load training session**:
   - Go to "My Training Sessions"
   - Find the desired session (use search/filter if needed)
   - Click "Load training" to load all drills from that session

## Installation

1. Upload the plugin folder to `/wp-content/plugins/`
2. Activate the plugin via the WordPress admin panel
3. Add the shortcodes to the desired pages

## CSS Customization

If you want to customize the styling, use the following CSS classes:

### New CSS classes (to avoid conflicts):
- `.ccc-mf-*` - Modern favorite buttons
- `.ccc-training-*` - Training session elements
- `.ccc-session-*` - Individual session cards
- `.ccc-dialog-*` - Pop-up dialogs

### Color variables:
```css
--ccc-brand: #F77F00;     /* Primary orange */
--ccc-brand2: #4169E1;    /* Royal blue */
--ccc-slate-*: /* Gray shades */
```

## Example Pages

### Page: Drills Overview
```html
[ccc_my_favorite_select_button text="Add to training"]
```

### Page: My Favorites
```html
[ccc_my_favorite_list_results posts_per_page="20" excerpt="100"]
[ccc_my_training_save_button text="Save as training"]
```

### Page: My Training Sessions
```html
[ccc_my_training_sessions_list title="My Hockey Training Sessions" show_search="true" show_filter="true"]
```

## Technical Details

- The plugin stores training sessions in WordPress user meta
- Supports both logged-in and non-logged-in users
- Non-logged-in users use localStorage
- Fully compatible with the original My Favorites functionality

## Shortcode Parameters

### [ccc_my_training_save_button]
- `text` - Button text (default: "Save Training Session")
- `class` - Additional CSS classes

### [ccc_my_training_sessions_list]
- `title` - Section title (default: "My Training Sessions")
- `show_search` - Show search bar (default: "true")
- `show_filter` - Show filter dropdown (default: "true")
- `class` - Additional CSS classes

### [ccc_my_favorite_select_button]
- `post_id` - Specific post ID (optional, uses current post if not specified)
- `text` - Button text (default: "Favorite")
- `style` - Style variant (default: "1")

### [ccc_my_favorite_list_results]
- `class` - Additional CSS classes
- `style` - Style variant (default: "1")
- `posts_per_page` - Number of posts to show (default: "100")
- `excerpt` - Excerpt character length (default: "0" - no excerpt)

## Support

For questions or issues, please contact the website administrator.