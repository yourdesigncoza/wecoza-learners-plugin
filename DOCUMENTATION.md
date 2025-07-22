# WeCoza Learners Management System Documentation

## Overview

The WeCoza Learners Management System is a comprehensive solution for managing learner information, assessments, and portfolios. This documentation covers the system architecture, components, and implementation details.

## System Architecture

### MVC Pattern Implementation

The system follows a Model-View-Controller (MVC) architecture:

- **Models**: Data structures and database interactions
- **Views**: Presentation layer and templates
- **Controllers**: Business logic and request handling

### Component Structure

```
@learners/
├── Core Components
│   ├── learners-plugin.php         # Main plugin file
│   ├── includes/                   # Core functionality
│   └── database/                   # Database operations
├── MVC Components
│   ├── models/                     # Data models
│   ├── views/                      # View templates
│   └── controllers/                # Controllers
├── Frontend Components
│   ├── shortcodes/                 # Shortcode implementations
│   ├── components/                 # Reusable UI components
│   └── assets/                     # CSS, JS, images
└── Backend Components
    ├── ajax/                       # AJAX handlers
    └── admin/                      # Admin functionality
```

## Database Schema

### Primary Tables

1. **learners**: Main table storing learner information
2. **learner_portfolios**: Portfolio file uploads
3. **learner_qualifications**: Educational qualification references
4. **learner_placement_level**: Assessment level references
5. **locations**: Cities, towns, and provinces
6. **employers**: Employer information

### Relationships

- Learners → Locations (city_town_id, province_region_id)
- Learners → Qualifications (highest_qualification)
- Learners → Employers (employer_id)
- Learners → Placement Levels (numeracy_level, communication_level)
- Learner Portfolios → Learners (learner_id)

## Shortcodes

### Active Shortcodes

#### 1. `[wecoza_display_learners]`
Displays all learners in a responsive Bootstrap table with:
- Sortable columns
- Search functionality
- Modal view for detailed information
- Edit and delete actions
- Responsive design

**Usage**: 
```
[wecoza_display_learners]
```

#### 2. `[wecoza_learners_form]`
Comprehensive learner registration form featuring:
- Personal information capture
- Address details with AJAX-loaded dropdowns
- Educational qualification selection
- Employment status and employer selection
- Assessment results input
- Multiple PDF portfolio uploads
- Form validation and error handling

**Usage**: 
```
[wecoza_learners_form]
```

#### 3. `[wecoza_learners_update_form]`
Update form for existing learners:
- Pre-populated with current data
- Portfolio management (view/delete existing, upload new)
- Same validation as capture form
- Requires `learner_id` URL parameter

**Usage**: 
```
[wecoza_learners_update_form]
```
Access via: `yoursite.com/update-learners/?learner_id=123`

### MVC-Based Shortcodes (Development)

These shortcodes are registered but currently show placeholder content:
- `[wecoza_learner_capture]`
- `[wecoza_learner_display]`
- `[wecoza_learner_update]`

## AJAX Endpoints

### Public Endpoints

1. **fetch_learners_dropdown_data**
   - Returns: Cities, provinces, qualifications, employers, placement levels
   - Used by: Forms for populating dropdowns

2. **fetch_learners_data**
   - Returns: HTML table rows of all learners
   - Used by: Display shortcode

3. **get_learner_data_by_id**
   - Parameters: learner_id
   - Returns: Complete learner object with mappings
   - Used by: View details modal

### Protected Endpoints (Require Authentication)

1. **update_learner**
   - Updates learner information
   - Validates all fields
   - Clears cache on success

2. **delete_learner**
   - Soft or hard delete learner
   - Parameters: learner_id
   - Returns success/error status

3. **delete_learner_portfolio**
   - Removes portfolio file
   - Updates database references
   - Requires admin capabilities

## File Upload Handling

### Portfolio Management

- **Allowed formats**: PDF only
- **Upload directory**: `/wp-content/uploads/portfolios/`
- **File naming**: Unique ID with timestamp
- **Database storage**: File paths stored in `learner_portfolios` table
- **Display format**: Comma-separated list in `learners.scanned_portfolio`

### Security Measures

- File type validation
- Unique file naming to prevent overwrites
- Directory protection
- Nonce verification on uploads

## JavaScript Components

### learners-app.js
Core JavaScript functionality:
- Form handling and validation
- AJAX communications
- Dynamic field toggling
- Modal management

### learners-display-shortcode.js
Display-specific functionality:
- DataTable initialization
- Search and sort features
- Modal population
- Delete confirmations

## CSS Organization

Current styles are in:
`/includes/css/ydcoza-styles.css`

Future plugin styles will be in:
`@learners/assets/css/learners-style.css`

### Style Categories

1. Form styles
2. Table styles
3. Modal styles
4. Component styles
5. Responsive styles

## Security Implementation

### Nonce Verification
All AJAX requests verify nonces:
```php
check_ajax_referer('learners_nonce', 'nonce', false)
```

### Data Sanitization
- Input sanitization using WordPress functions
- Prepared statements for database queries
- Output escaping in templates

### Permission Checks
- Role-based access for admin functions
- Capability checks for sensitive operations

## Performance Optimization

### Caching Strategy
- WordPress transients for frequently accessed data
- 12-hour cache duration for learner listings
- Cache invalidation on updates

### Database Optimization
- Indexed columns for search performance
- Efficient JOIN queries
- Pagination support (ready for implementation)

## Error Handling

### Frontend Errors
- User-friendly error messages
- Form validation feedback
- AJAX error handling with fallbacks

### Backend Errors
- Comprehensive error logging
- Exception handling
- Transaction rollback on failures

## Migration Guide

### Converting to Plugin

1. **File Structure Changes**
   ```php
   // Replace
   WECOZA_CHILD_DIR
   WECOZA_CHILD_URL
   
   // With
   WECOZA_LEARNERS_PLUGIN_DIR
   WECOZA_LEARNERS_PLUGIN_URL
   ```

2. **Namespace Updates**
   - Ensure all class namespaces are correct
   - Update autoloader if implemented

3. **Path Updates**
   - Update all file includes
   - Update asset URLs
   - Update AJAX URLs

4. **Database Considerations**
   - Table prefix handling
   - Migration scripts for existing data

## Troubleshooting

### Common Issues

1. **Dropdowns not populating**
   - Check AJAX URL configuration
   - Verify nonce generation
   - Check database connections

2. **File uploads failing**
   - Verify directory permissions
   - Check PHP upload limits
   - Confirm file type restrictions

3. **Data not displaying**
   - Clear transient cache
   - Check database queries
   - Verify user permissions

## Future Enhancements

### Planned Features
1. Bulk import/export functionality
2. Advanced reporting
3. Email notifications
4. API endpoints
5. Mobile app integration

### Extension Points
- Custom hooks and filters
- Template overrides
- Additional shortcode parameters
- Custom post type integration