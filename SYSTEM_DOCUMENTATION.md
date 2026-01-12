# IT Inventory Management System - Complete Functionality Documentation

## Table of Contents
1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [User Roles and Permissions](#user-roles-and-permissions)
4. [Core Features](#core-features)
5. [API Endpoints](#api-endpoints)
6. [Database Structure](#database-structure)
7. [Workflows](#workflows)
8. [Technical Implementation](#technical-implementation)
9. [Factory-Specific Features](#factory-specific-features)

---

## System Overview

The **IT Inventory Management System** is a comprehensive web-based application designed for Ananta Companies to manage IT assets across multiple factory locations. The system provides centralized asset tracking, maintenance management, approval workflows, and user management capabilities.

### Key Characteristics
- **Multi-factory Support**: Manages assets for Head Office, AGL, AJL, PWPL, and ABM factories
- **Role-based Access Control**: Different dashboards and permissions for different user types
- **Approval Workflow System**: Requests require admin approval before execution
- **Service History Tracking**: Complete maintenance and service records
- **Asset Lifecycle Management**: From addition to deletion with full audit trail

---

## Architecture

### Technology Stack
- **Backend**: PHP (PDO for database operations)
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Session Management**: PHP Sessions
- **Authentication**: Password hashing with bcrypt

### System Structure
```
IT-Inventory-Management-System/
├── Core Files
│   ├── db_connect.php          # Database connection
│   ├── auth.php                 # Authentication helper
│   ├── db_functions.php         # Database utility functions
│   └── logout.php               # Session termination
│
├── Dashboard Files
│   ├── dashboard.php            # Head Office dashboard
│   ├── dashboard_admin.php      # Super Admin dashboard
│   ├── dashboard_agl.php        # AGL factory dashboard
│   ├── dashboard_ajl.php        # AJL factory dashboard
│   ├── dashboard_abm.php        # ABM factory dashboard
│   └── dashboard_pwpl.php       # PWPL factory dashboard
│
├── API Endpoints
│   ├── get_assets*.php          # Asset retrieval (factory-specific)
│   ├── get_all_assets.php
│   ├── add_asset*.php            # Asset creation
│   ├── delete_asset*.php        # Asset removal
│   ├── update_status*.php       # Status updates
│   ├── get_service_history*.php  # Service records
│   └── get_deleted_assets.php   # Archived assets
│
├── Approval System
│   ├── approval_functions.php    # Approval logic
│   ├── process_approval.php     # Approval processing
│   ├── request_approval*.php    # Request creation
│   ├── get_pending_approvals.php
│   └── count_pending_approvals.php
│
├── User Management
│   ├── create_user.php
│   ├── get_users.php
│   └── delete_user.php
│
└── Notification System
    ├── notification_functions.php
    ├── get_notifications.php
    └── mark_notifications_read.php
```

---

## User Roles and Permissions

### 1. Super Admin
- **Access**: `dashboard_admin.php`
- **Credentials**: Special hardcoded credentials (superadmin/superadmin2 with password '1234')
- **Permissions**:
  - Full system access
  - View all factories' assets
  - Manage all users
  - Process all approval requests
  - View complete service history
  - Access deleted assets archive

### 2. Admin (Head Office)
- **Access**: `dashboard.php`
- **Permissions**:
  - Add new assets (requires approval)
  - Remove assets (requires approval)
  - Send assets for servicing
  - Complete service requests
  - View service history
  - Process pending approvals
  - User management (create/delete users)
  - Export data to CSV
  - View deleted assets
  - Filter by factory and status

### 3. Factory Users (AGL, AJL, ABM, PWPL)
- **Access**: Factory-specific dashboards (`dashboard_agl.php`, etc.)
- **Permissions**:
  - View factory-specific assets
  - Request asset addition (requires approval)
  - Request asset removal (requires approval)
  - Request servicing (requires approval)
  - View service history for their factory
  - View deleted assets for their factory
  - Limited to their factory's data

### 4. Regular Users
- **Access**: Standard dashboard based on factory
- **Permissions**:
  - View assets
  - View service history
  - Limited read-only operations

---

## Core Features

### 1. Asset Management

#### Asset Addition
- **Process**: 
  1. User fills asset form (name, category, brand, model, serial, location, etc.)
  2. System generates Asset ID:
     - Head Office: `HOIT-001`, `HOIT-002`, etc.
     - Other factories: `{FACTORY}-IT-{TIMESTAMP}`
  3. For non-admin users: Creates approval request
  4. For admin users: Direct addition (or approval-based, depending on configuration)
- **Required Fields**:
  - Asset Name, Category, Brand, Model, Serial Number
  - Location, Department, Purchase Date, Purchase Price, Warranty Expiry
- **Optional Fields**: Assigned To, Priority, Notes
- **Categories**: Desktop, Laptop, Server, Network, Printer, Monitor, Mobile

#### Asset Viewing
- **Filtering Options**:
  - By Factory (Head Office, AGL, AJL, ABM, PWPL)
  - By Status (Active, Inactive, Out of Order, Maintenance)
  - By Search (text search across all fields)
- **Display Columns**:
  - Asset ID, Name, Category, Brand, Model, Serial Number
  - Status, Location, Assigned To, Department
  - Purchase Date, Purchase Price, Warranty Expiry
  - Last Maintenance, Priority, Notes
- **Statistics Dashboard**:
  - Active Assets count
  - Inactive Assets count
  - Out of Order count
  - Under Maintenance count

#### Asset Status Management
- **Status Types**:
  - `ACTIVE`: Asset is operational
  - `INACTIVE`: Asset is not in use
  - `OUT OF ORDER`: Asset is broken/needs repair
  - `MAINTENANCE`: Asset is being serviced
- **Status Transitions**:
  - Active/Out of Order → Maintenance (when sent for service)
  - Maintenance → Active (when service completed)

#### Asset Removal
- **Process**:
  1. User selects asset to remove
  2. Provides removal reason (End of Life, Sold, Donated, Disposed, Lost/Stolen, Transfer)
  3. Adds removal notes
  4. Asset is archived to `deleted_assets` table
  5. Asset removed from active inventory
  6. Removal tracked with: reason, notes, removed_by, removal_date

### 2. Service Management

#### Send for Servicing
- **Process**:
  1. User selects asset (must be Active or Out of Order)
  2. Specifies service type (Scheduled Maintenance, Repair, Upgrade, Inspection)
  3. Enters technician name
  4. Adds service notes
  5. Asset status changes to `MAINTENANCE`
  6. Service record created in `service_history` table
- **Service Types**:
  - Scheduled Maintenance
  - Repair
  - Upgrade
  - Inspection

#### Complete Service
- **Process**:
  1. Admin selects asset in Maintenance status
  2. Adds completion notes
  3. Asset status changes to `ACTIVE`
  4. Service record updated with completion date
  5. Status changed to `COMPLETED`

#### Service History
- **Viewing Options**:
  - Per asset (detailed history for specific asset)
  - Per factory (all service records for a factory)
  - All factories (complete system-wide history)
- **History Details**:
  - Asset ID and Name
  - Service Date
  - Service Type
  - Technician Name
  - Status (Pending/Completed)
  - Completion Date
  - Service Notes
  - Completion Notes
  - Factory Location

### 3. Approval Workflow System

#### Approval Request Types
1. **ADD**: Request to add new asset
2. **SERVICE**: Request to send asset for servicing
3. **COMPLETE_SERVICE**: Request to complete service (if approval required)
4. **DELETE**: Request to remove asset

#### Approval Process
1. **Request Creation**:
   - User initiates action (add/delete/service)
   - System creates entry in `pending_approvals` table
   - Action details stored as JSON in `action_details` field
   - Status set to `PENDING`
   - Notification created for admin

2. **Admin Review**:
   - Admin views pending approvals in modal
   - Sees request details (asset info, requester, date, factory)
   - Can view full action details

3. **Approval Decision**:
   - **APPROVE**: Action is executed immediately
     - Asset added/deleted/service initiated
     - Approval record updated
     - Notification sent to requester
   - **REJECT**: Action is cancelled
     - Approval record marked as rejected
     - Notification sent to requester
     - No changes to assets

#### Approval Details Storage
- Action details stored as JSON containing:
  - For ADD: All asset fields (name, category, brand, model, etc.)
  - For SERVICE: Service type, technician, notes, dates
  - For DELETE: Removal reason, notes, dates
  - For COMPLETE_SERVICE: Completion notes, dates

### 4. User Management

#### User Creation
- **Fields**:
  - Full Name (required)
  - Username (required, unique)
  - Password (required, hashed with bcrypt)
  - Employee ID (required)
  - User Type (user/admin)
  - Factory (AGL, AJL, ABM, PWPL, Head Office)
- **Process**:
  - Admin creates user via User Management modal
  - Password is hashed before storage
  - User can immediately log in

#### User Viewing
- List all users with:
  - Name, Username, Employee ID
  - User Type, Factory
  - Actions (Delete)

#### User Deletion
- Admin can delete users
- Confirmation required before deletion

### 5. Notification System

#### Notification Types
- Approval request created
- Approval approved
- Approval rejected
- Service completed
- Asset status changes

#### Notification Features
- Unread notification count display
- Mark as read functionality
- Notification history
- Real-time updates (on page refresh)

### 6. Data Export

#### CSV Export
- Export filtered asset data to CSV
- Includes all asset columns
- Filename includes export date
- Downloads automatically

### 7. Deleted Assets Archive

#### Viewing Deleted Assets
- Filter by factory
- View complete asset details
- View removal information:
  - Removal reason
  - Removal notes
  - Removed by (user)
  - Removal date
- Historical record of all removed assets

---

## API Endpoints

### Asset Management

#### `get_assets.php`
- **Method**: GET
- **Description**: Retrieves all assets (Head Office)
- **Response**: JSON array of assets

#### `get_assets_agl.php`, `get_assets_ajl.php`, `get_assets_abm.php`
- **Method**: GET
- **Description**: Retrieves factory-specific assets
- **Response**: JSON array of assets

#### `get_all_assets.php`
- **Method**: GET
- **Description**: Retrieves assets from all factories
- **Response**: JSON array of assets

#### `add_asset.php`
- **Method**: POST
- **Body**: JSON with asset details
- **Description**: Adds new asset (Head Office, direct)
- **Response**: `{success: true, asset_id: "HOIT-001"}`

#### `add_asset_agl.php`, `add_asset_ajl.php`, `add_asset_abm.php`
- **Method**: POST
- **Body**: JSON with asset details
- **Description**: Creates approval request for asset addition
- **Response**: `{success: true, message: "Approval requested"}`

#### `delete_asset.php`
- **Method**: POST
- **Body**: JSON with `{asset_id, remove_reason, remove_notes}`
- **Description**: Removes asset (Head Office, direct)
- **Response**: `{success: true}`

#### `delete_asset_agl.php`, `delete_asset_ajl.php`, `delete_asset_abm.php`
- **Method**: POST
- **Body**: JSON with asset details
- **Description**: Creates approval request for asset removal
- **Response**: `{success: true, message: "Approval requested"}`

#### `update_status.php`
- **Method**: POST
- **Body**: JSON with `{asset_id, status, service_type, service_notes, service_by, last_maintenance}`
- **Description**: Updates asset status and creates service record
- **Response**: `{success: true}`

#### `update_status_agl.php`, `update_status_ajl.php`, `update_status_abm.php`
- **Method**: POST
- **Body**: JSON with service details
- **Description**: Creates approval request for service
- **Response**: `{success: true, message: "Approval requested"}`

#### `complete_service.php`
- **Method**: POST
- **Body**: JSON with `{asset_id, completion_notes}`
- **Description**: Completes service and sets asset to Active
- **Response**: `{success: true}`

#### `complete_service_agl.php`, `complete_service_ajl.php`, `complete_service_abm.php`
- **Method**: POST
- **Description**: Creates approval request for service completion
- **Response**: `{success: true, message: "Approval requested"}`

### Service History

#### `get_service_history.php`
- **Method**: GET
- **Parameters**: 
  - `asset_id` (optional): Specific asset ID
  - `factory` (optional): Factory filter
- **Description**: Retrieves service history
- **Response**: JSON array of service records

#### `get_service_history_agl.php`, `get_service_history_ajl.php`, `get_service_history_abm.php`
- **Method**: GET
- **Description**: Factory-specific service history
- **Response**: JSON array of service records

### Approval System

#### `get_pending_approvals.php`
- **Method**: GET
- **Description**: Retrieves all pending approval requests
- **Response**: JSON array of approvals

#### `count_pending_approvals.php`
- **Method**: GET
- **Description**: Returns count of pending approvals
- **Response**: `{success: true, count: 5}`

#### `process_approval.php`
- **Method**: POST
- **Body**: JSON with `{approval_id, action: "APPROVE"/"REJECT", approver}`
- **Description**: Processes approval decision
- **Response**: `{success: true, message: "..."}`

#### `request_approval_agl.php`, `request_approval_ajl.php`, `request_approval_abm.php`
- **Method**: POST
- **Body**: JSON with approval request details
- **Description**: Creates approval request
- **Response**: `{success: true}`

### User Management

#### `create_user.php`
- **Method**: POST
- **Body**: JSON with user details
- **Description**: Creates new user
- **Response**: `{success: true, message: "User created"}`

#### `get_users.php`
- **Method**: GET
- **Description**: Retrieves all users
- **Response**: JSON array of users

#### `delete_user.php`
- **Method**: DELETE
- **Parameters**: `id` (user ID)
- **Description**: Deletes user
- **Response**: `{success: true}`

### Notifications

#### `get_notifications.php`
- **Method**: GET
- **Description**: Retrieves user notifications
- **Response**: JSON array of notifications

#### `get_unread_notifications.php`
- **Method**: GET
- **Description**: Returns unread notification count
- **Response**: `{count: 3}`

#### `mark_notifications_read.php`
- **Method**: POST
- **Description**: Marks notifications as read
- **Response**: `{success: true}`

### Deleted Assets

#### `get_deleted_assets.php`
- **Method**: GET
- **Parameters**: `factory` (required)
- **Description**: Retrieves deleted assets for factory
- **Response**: JSON array of deleted assets

---

## Database Structure

### Core Tables

#### `users`
- `id` (INT, PRIMARY KEY)
- `name` (VARCHAR)
- `username` (VARCHAR, UNIQUE)
- `password` (VARCHAR, hashed)
- `employee_id` (VARCHAR)
- `user_type` (ENUM: 'user', 'admin', 'super_admin')
- `factory` (VARCHAR: 'agl', 'ajl', 'abm', 'pwpl', 'head office')
- `created_at` (TIMESTAMP)

#### `assets` (Head Office)
- `asset_id` (VARCHAR, PRIMARY KEY) - Format: HOIT-001
- `asset_name` (VARCHAR)
- `category` (VARCHAR)
- `brand` (VARCHAR)
- `model` (VARCHAR)
- `serial_number` (VARCHAR)
- `status` (ENUM: 'ACTIVE', 'INACTIVE', 'OUT OF ORDER', 'MAINTENANCE')
- `location` (VARCHAR)
- `assigned_to` (VARCHAR)
- `department` (VARCHAR)
- `purchase_date` (DATE)
- `purchase_price` (DECIMAL)
- `warranty_expiry` (DATE)
- `last_maintenance` (DATE)
- `priority` (ENUM: 'Low', 'Medium', 'High')
- `notes` (TEXT)

#### `assets_agl`, `assets_ajl`, `assets_abm`, `assets_pwpl`
- Same structure as `assets` table
- Factory-specific asset storage

#### `service_history` (Head Office)
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `asset_id` (VARCHAR, FOREIGN KEY)
- `service_date` (DATE)
- `service_type` (VARCHAR)
- `service_notes` (TEXT)
- `service_by` (VARCHAR)
- `status` (ENUM: 'PENDING', 'COMPLETED')
- `completion_date` (DATE)
- `completion_notes` (TEXT)

#### `service_history_agl`, `service_history_ajl`, `service_history_abm`, `service_history_pwpl`
- Same structure as `service_history`
- Factory-specific service records

#### `pending_approvals`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `asset_id` (VARCHAR)
- `action_type` (ENUM: 'ADD', 'SERVICE', 'COMPLETE_SERVICE', 'DELETE')
- `requested_by` (VARCHAR)
- `factory` (VARCHAR)
- `current_status` (VARCHAR)
- `action_details` (TEXT, JSON format)
- `status` (ENUM: 'PENDING', 'APPROVED', 'REJECTED')
- `request_date` (TIMESTAMP)
- `approved_by` (VARCHAR)
- `approval_date` (TIMESTAMP)

#### `deleted_assets` (Head Office)
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `original_asset_id` (VARCHAR)
- `asset_name`, `category`, `brand`, `model`, `serial_number`
- `status`, `location`, `assigned_to`, `department`
- `purchase_date`, `purchase_price`, `warranty_expiry`
- `last_maintenance`, `priority`, `notes`
- `removal_reason` (VARCHAR)
- `removal_notes` (TEXT)
- `removed_by` (VARCHAR)
- `removal_date` (TIMESTAMP)

#### `deleted_assets_agl`, `deleted_assets_ajl`, `deleted_assets_abm`, `deleted_assets_pwpl`
- Same structure as `deleted_assets`
- Factory-specific deleted asset archives

#### `notifications`
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` (INT, FOREIGN KEY)
- `message` (TEXT)
- `approval_id` (INT, FOREIGN KEY, nullable)
- `is_read` (BOOLEAN, default: 0)
- `created_at` (TIMESTAMP)

---

## Workflows

### Workflow 1: Adding New Asset (Factory User)

```
1. Factory User logs in → Factory Dashboard
2. Clicks "Add New Asset"
3. Fills asset form
4. Submits → Creates approval request
5. System stores in pending_approvals
6. Admin receives notification
7. Admin reviews in Pending Approvals modal
8. Admin approves/rejects
9. If approved:
   - Asset added to assets_{factory} table
   - Asset ID generated
   - Notification sent to requester
10. If rejected:
   - Approval marked as rejected
   - Notification sent to requester
```

### Workflow 2: Sending Asset for Service (Factory User)

```
1. Factory User selects asset
2. Clicks "Send for Servicing"
3. Fills service form (type, technician, notes)
4. Submits → Creates approval request
5. Admin reviews and approves
6. If approved:
   - Asset status → MAINTENANCE
   - Service record created in service_history_{factory}
   - Notification sent
7. Asset appears in Maintenance status
```

### Workflow 3: Completing Service (Head Office Admin)

```
1. Admin views assets in Maintenance status
2. Clicks "Complete" button on asset
3. Adds completion notes
4. Submits → Service completed
5. Asset status → ACTIVE
6. Service record updated with completion_date
7. Status changed to COMPLETED
```

### Workflow 4: Removing Asset (Factory User)

```
1. Factory User selects asset
2. Clicks "Remove Asset"
3. Selects removal reason
4. Adds removal notes
5. Submits → Creates approval request
6. Admin reviews and approves
7. If approved:
   - Asset copied to deleted_assets_{factory}
   - Asset removed from assets_{factory}
   - Removal details recorded
   - Notification sent
```

### Workflow 5: User Management (Admin)

```
1. Admin clicks "User Management"
2. Views list of all users
3. Can:
   - Create new user (form)
   - Delete existing user (with confirmation)
4. New user can immediately log in
```

---

## Technical Implementation

### Authentication System
- **Login Process**:
  - Username, password, and location required
  - Special handling for superadmin credentials
  - Password verification using `password_verify()`
  - Session variables set: `user_id`, `username`, `user_type`, `factory`
  - Redirect to appropriate dashboard based on factory

- **Session Management**:
  - Session started on login
  - Protected pages check `$_SESSION['user_id']`
  - Redirect to login if not authenticated
  - Logout destroys session

### Asset ID Generation
- **Head Office**: Sequential format `HOIT-001`, `HOIT-002`, etc.
  - Queries max existing HOIT ID
  - Increments and pads with zeros
- **Other Factories**: Format `{FACTORY}-IT-{TIMESTAMP}`
  - Uses factory code and current timestamp

### Factory-Specific Data Isolation
- Each factory has separate tables:
  - `assets_{factory}`
  - `service_history_{factory}`
  - `deleted_assets_{factory}`
- Head Office uses base table names:
  - `assets`
  - `service_history`
  - `deleted_assets`

### Approval System Logic
- **Request Creation**: Stores action details as JSON
- **Approval Processing**: 
  - Decodes JSON action details
  - Executes action on appropriate factory table
  - Updates approval status
  - Creates notifications
- **Action Execution**: Factory-aware table selection

### Data Filtering
- **Client-side**: JavaScript filters on loaded data
- **Server-side**: Factory-specific endpoints
- **Combined**: Factory filter + status filter + search

### Error Handling
- Try-catch blocks in all PHP files
- JSON error responses
- User-friendly error messages
- Database transaction rollback on errors

---

## Factory-Specific Features

### Head Office (dashboard.php)
- **Full Access**: All features available
- **Direct Actions**: Add/delete without approval (for admin)
- **Approval Processing**: Can approve/reject all requests
- **Cross-factory View**: Can view all factories' data
- **User Management**: Full CRUD operations
- **Export**: CSV export functionality

### AGL Factory (dashboard_agl.php)
- **Factory-specific**: Only AGL assets visible
- **Approval-based**: All modifications require approval
- **Limited Actions**: View, request add/delete/service
- **Service History**: Only AGL service records

### AJL Factory (dashboard_ajl.php)
- Same as AGL, but for AJL factory
- Uses `assets_ajl`, `service_history_ajl` tables

### ABM Factory (dashboard_abm.php)
- Same as AGL, but for ABM factory
- Uses `assets_abm`, `service_history_abm` tables

### PWPL Factory (dashboard_pwpl.php)
- Same as AGL, but for PWPL factory
- Uses `assets_pwpl`, `service_history_pwpl` tables

---

## Security Features

1. **Password Hashing**: Bcrypt password hashing
2. **SQL Injection Prevention**: PDO prepared statements
3. **Session Management**: Secure session handling
4. **Access Control**: Role-based permissions
5. **Input Validation**: Server-side validation
6. **XSS Prevention**: `htmlspecialchars()` usage

---

## UI/UX Features

1. **Responsive Design**: Mobile-friendly layouts
2. **Modal Dialogs**: For forms and details
3. **Loading Indicators**: Visual feedback during operations
4. **Status Badges**: Color-coded status indicators
5. **Priority Indicators**: Visual priority levels
6. **Statistics Dashboard**: Quick overview cards
7. **Search and Filter**: Real-time filtering
8. **Export Functionality**: CSV download

---

## Future Enhancement Possibilities

1. **Email Notifications**: Email alerts for approvals
2. **Asset Barcode Scanning**: QR code integration
3. **Reporting Dashboard**: Advanced analytics
4. **Asset Transfer**: Between factories
5. **Warranty Alerts**: Expiry notifications
6. **Maintenance Scheduling**: Automated reminders
7. **Asset Depreciation**: Financial tracking
8. **Multi-language Support**: Internationalization
9. **API Authentication**: Token-based API access
10. **Audit Log**: Complete action history

---

## System Maintenance

### Database Backup
- Regular backups of all tables
- Factory-specific table backups
- Deleted assets archive preservation

### Performance Optimization
- Indexed database columns
- Efficient queries
- Client-side filtering for responsiveness

### Monitoring
- Error logging in PHP
- Database error tracking
- User action logging (via approval system)

---

## Conclusion

This IT Inventory Management System provides a comprehensive solution for managing IT assets across multiple factory locations. With its approval workflow, factory-specific data isolation, service history tracking, and role-based access control, it ensures secure and organized asset management for Ananta Companies.

The system is designed to be scalable, maintainable, and user-friendly, with clear separation of concerns and well-structured codebase.

---

**Document Version**: 1.0  
**Last Updated**: 2024  
**System**: IT Inventory Management System  
**Organization**: Ananta Companies

