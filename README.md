# Gurukul Events

A WordPress plugin for managing spiritual and religious events like Anusthan, Deeksha, Teaching, Sadhana Shivir, and more.

## Features

- Custom event post type
- Event categories with distinct styling
- Event details including date, time, location, and organizer
- Dakshina (contribution) management
- Shortcode support for displaying events
- Responsive grid and list layouts
- Customizable registration system
- Professional design and layout

## Installation

1. Download the plugin
2. Upload to your WordPress site
3. Activate the plugin
4. Go to Events → Settings to configure

## Usage

### Basic Usage
Create and manage events from the WordPress admin panel under the "Events" menu.

### Shortcode
Use the shortcode to display events on any page:
`[gurukul_events]`

### Shortcode Parameters
- `category`: Filter by event category
- `limit`: Number of events to display (default: 6)
- `orderby`: Sort by date, title, event_date (default: date)
- `order`: ASC or DESC (default: ASC)
- `view`: grid or list (default: grid)

Example:
`[gurukul_events limit="3" category="Anusthan" view="grid"]`

## Development

Built with WordPress best practices and modern PHP standards.

## License

GPL v2 or later 