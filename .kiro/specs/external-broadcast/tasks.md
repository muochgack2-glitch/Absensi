# Implementation Plan: External Broadcast

## Overview

This implementation plan breaks down the External Broadcast feature into atomic, testable tasks. The feature extends the SPMB WhatsApp Gateway to support broadcasting messages to external recipients (alumni, CSV uploads, manual lists) with duplicate detection against the SPMB database.

**Technology Stack:** PHP 8.x, Laravel 11.x, MySQL 8.x, Blade templates, Bootstrap 5, JavaScript ES6+

**Key Components:**
- 3 database migrations (modify whatsapp_logs, create external_broadcast_batches, create external_broadcast_recipients)
- 2 new models (ExternalBroadcastBatch, ExternalBroadcastRecipient)
- 1 modified model (WhatsAppLog)
- 1 new service (ExternalBroadcastService)
- 5 new controller methods + 1 modified method in WhatsAppController
- 5 new routes
- 2 modified Blade views (broadcast page with tabs, phone list with external tab)
- JavaScript for CSV upload, manual input parsing, and UI interactions

## Tasks

- [ ] 1. Database schema setup
  - [ ] 1.1 Create migration to add external_batch_id to whatsapp_logs table
    - Create migration file with `external_batch_id` column (nullable bigint unsigned)
    - Add foreign key constraint to external_broadcast_batches table
    - Add index on external_batch_id for query performance
    - _Requirements: 12.1_
  
  - [ ] 1.2 Create migration for external_broadcast_batches table
    - Create table with columns: id, batch_name, description, total_recipients, total_sent, total_failed, status (enum), source_type (enum), source_file, created_by, timestamps, completed_at
    - Add indexes on created_by, status, and created_at
    - Add foreign key to users table for created_by
    - _Requirements: 12.2_
  
  - [ ] 1.3 Create migration for external_broadcast_recipients table
    - Create table with columns: id, batch_id, name, phone, phone_normalized, notes, is_duplicate_spmb (boolean), matched_pendaftar_id, timestamps
    - Add indexes on phone_normalized, batch_id, and is_duplicate_spmb for performance
    - Add foreign key to external_broadcast_batches (cascade on delete)
    - _Requirements: 12.3_

- [ ] 2. Create Eloquent models
  - [ ] 2.1 Create ExternalBroadcastBatch model
    - Define fillable attributes and casts
    - Add relationships: recipients (hasMany), logs (hasMany), creator (belongsTo User)
    - Implement helper methods: markAsInProgress(), markAsCompleted(), incrementSent(), incrementFailed()
    - _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_
  
  - [ ] 2.2 Create ExternalBroadcastRecipient model
    - Define fillable attributes with boolean cast for is_duplicate_spmb
    - Add relationships: batch (belongsTo), matchedPendaftar (belongsTo Pendaftar)
    - Implement messages() method to retrieve WhatsAppLog entries by phone and batch
    - _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 2.6_
  
  - [ ] 2.3 Modify WhatsAppLog model
    - Add external_batch_id to fillable array
    - Add externalBatch relationship (belongsTo ExternalBroadcastBatch)
    - Update getTypeLabelAttribute to include 'external_broadcast' type
    - _Requirements: 7.2, 7.3, 7.4, 7.5_

- [ ] 3. Implement ExternalBroadcastService
  - [ ] 3.1 Create service class with phone normalization method
    - Implement normalizePhone() method to convert phone numbers to 62xxx format
    - Handle edge cases: leading 0, leading 8, missing country code
    - Validate length (10-15 digits) and return null for invalid formats
    - _Requirements: 13.1, 13.2, 13.3, 13.4, 13.6_
  
  - [ ]* 3.2 Write property test for phone normalization
    - **Property 1: Phone Number Normalization Consistency**
    - **Validates: Requirements 13.1, 13.2, 13.3, 13.4**
    - Test that normalizePhone(P) always returns the same result for the same input
    - Verify output matches regex: /^62[0-9]{9,13}$/
  
  - [ ] 3.3 Implement CSV parsing method
    - Create parseCSV() method accepting UploadedFile
    - Validate CSV header contains 'name' and 'phone' columns
    - Parse rows and collect validation errors with row numbers
    - Return array of recipients with normalized phone numbers
    - _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 3.7, 3.8_
  
  - [ ] 3.4 Implement manual input parsing method
    - Create parseManualInput() method accepting string with newline-separated entries
    - Support two formats: phone only OR "phone|name|notes"
    - Use "External Contact" as default name when only phone provided
    - Limit to 500 entries and validate each line
    - _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5, 4.6_
  
  - [ ] 3.5 Implement duplicate detection algorithm
    - Create detectDuplicates() method accepting array of recipients
    - Query SPMB database (pendaftar table) for matches in no_hp_wali, no_hp_ortu, no_telepon
    - Use batch query with whereIn for performance (O(n) complexity)
    - Flag recipients with is_duplicate and set matched_pendaftar_id
    - _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 8.6, 8.7_
  
  - [ ]* 3.6 Write property test for duplicate detection
    - **Property 2: Duplicate Detection Accuracy**
    - **Validates: Requirements 8.1, 8.2, 8.3, 8.4**
    - Test that if phone P exists in SPMB database, detectDuplicates must flag it
    - Verify no false negatives occur
    - Measure detection time is O(n) where n is number of recipients
  
  - [ ] 3.7 Implement batch creation method
    - Create createBatch() method accepting name, source type, and user ID
    - Create ExternalBroadcastBatch record with status 'pending'
    - Return batch model instance
    - _Requirements: 1.1, 1.2, 1.5, 15.5_
  
  - [ ] 3.8 Implement recipient saving method
    - Create saveRecipients() method accepting batch ID and recipients array
    - Use database transaction to ensure atomicity
    - Deduplicate phone numbers within the same batch
    - Bulk insert ExternalBroadcastRecipient records
    - _Requirements: 2.1, 11.4, 15.5_
  
  - [ ]* 3.9 Write property test for batch atomicity
    - **Property 3: Batch Atomicity**
    - **Validates: Requirements 15.5**
    - Test that creating a batch with recipients is atomic (transaction)
    - Verify that if any recipient fails validation, entire batch creation rolls back
    - Verify batch status transitions are sequential: pending → in_progress → completed/failed

- [ ] 4. Checkpoint - Ensure models and service layer tests pass
  - Run migrations on test database
  - Verify model relationships work correctly
  - Test ExternalBroadcastService methods independently
  - Ensure all tests pass, ask the user if questions arise

- [ ] 5. Implement controller methods in WhatsAppController
  - [ ] 5.1 Create externalBroadcastPage method
    - Return Blade view with active WhatsApp templates
    - Pass templates collection to view
    - Apply admin_wa middleware for authorization
    - _Requirements: 5.1, 5.2, 14.1_
  
  - [ ] 5.2 Create parseExternalRecipients method
    - Accept POST request with source_type (csv|manual), file or text input, batch_name
    - Validate input: required batch_name, unique within 30 days, file or manual_input required
    - Call ExternalBroadcastService to parse CSV or manual input
    - Run duplicate detection on parsed recipients
    - Create batch and save recipients in transaction
    - Return JSON with batch_id, preview (first 10 recipients), total_count, duplicates_count
    - _Requirements: 3.1-3.8, 4.1-4.6, 11.1, 11.2, 11.6_
  
  - [ ] 5.3 Create sendExternalBroadcast method
    - Accept POST request with batch_id, message, optional template_id
    - Validate WhatsApp Gateway is connected before proceeding
    - Load batch and recipients from database
    - Mark batch as 'in_progress'
    - Loop through recipients and send messages via WhatsAppService
    - Create WhatsAppLog entries with external_batch_id and type 'external_broadcast'
    - Apply 1 second delay between messages (rate limiting)
    - Update batch total_sent/total_failed counters
    - Mark batch as 'completed' when done
    - Return JSON with success status and batch statistics
    - _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7, 10.1, 10.2, 10.3, 10.4, 10.5, 11.3, 11.5, 15.3_
  
  - [ ] 5.4 Create getExternalMessages method
    - Accept external recipient ID as parameter
    - Load ExternalBroadcastRecipient with message history
    - Return JSON with recipient details and WhatsAppLog entries
    - _Requirements: 6.5_
  
  - [ ] 5.5 Modify phoneList method to handle external tab
    - Check for 'tab=external' query parameter
    - When external tab active, query ExternalBroadcastRecipient with batch relationship
    - Support 'show_duplicates_only' filter
    - Paginate results (20 per page)
    - Return view with external recipients data
    - _Requirements: 6.1, 6.2, 6.3, 6.4, 15.6_
  
  - [ ]* 5.6 Write integration tests for controller methods
    - Test parseExternalRecipients with valid CSV and manual input
    - Test sendExternalBroadcast flow end-to-end with mocked WhatsAppService
    - Test phoneList external tab filtering and pagination
    - _Requirements: 5.1-5.6, 6.1-6.6, 7.1-7.7_

- [ ] 6. Define routes in web.php
  - [ ] 6.1 Add route for external broadcast page
    - Route: GET /whatsapp/broadcast/external
    - Controller method: WhatsAppController@externalBroadcastPage
    - Middleware: auth, CheckRole:admin_wa
    - _Requirements: 5.1, 14.1_
  
  - [ ] 6.2 Add route for parsing external recipients
    - Route: POST /whatsapp/broadcast/external/parse
    - Controller method: WhatsAppController@parseExternalRecipients
    - Middleware: auth, CheckRole:admin_wa
    - _Requirements: 5.2, 14.4_
  
  - [ ] 6.3 Add route for sending external broadcast
    - Route: POST /whatsapp/broadcast/external/send
    - Controller method: WhatsAppController@sendExternalBroadcast
    - Middleware: auth, CheckRole:admin_wa
    - _Requirements: 5.3, 14.4_
  
  - [ ] 6.4 Add route for external recipient messages
    - Route: GET /whatsapp/external/{id}/messages
    - Controller method: WhatsAppController@getExternalMessages
    - Middleware: auth, CheckRole:admin_wa
    - _Requirements: 6.5_
  
  - [ ] 6.5 Modify existing phone list route to support external tab
    - Existing route: GET /whatsapp/phone-list accepts ?tab=external parameter
    - No new route needed, just modify controller logic (already done in task 5.5)
    - _Requirements: 6.1_

- [ ] 7. Create Blade view for external broadcast tab
  - [ ] 7.1 Modify broadcast.blade.php to add tab navigation
    - Add Bootstrap tabs: "Data SPMB" (existing) and "Data Eksternal" (new)
    - Keep existing SPMB broadcast form in first tab
    - Create second tab container for external broadcast UI
    - _Requirements: 5.1, 5.2_
  
  - [ ] 7.2 Implement data source selection in external tab
    - Add button group radio inputs for CSV vs Manual input
    - Show/hide CSV upload section or manual textarea based on selection
    - _Requirements: 3.1, 4.1_
  
  - [ ] 7.3 Create CSV upload section
    - Add file input accepting .csv files (max 2MB)
    - Display format hint: "name,phone,notes (header required)"
    - _Requirements: 3.1, 3.2_
  
  - [ ] 7.4 Create manual input section
    - Add textarea with placeholder showing format examples
    - Display format hint: "phone|name|notes (one per line)"
    - _Requirements: 4.1, 4.2_
  
  - [ ] 7.5 Add batch name input field
    - Text input for batch name with placeholder
    - Display validation error if duplicate name in last 30 days
    - _Requirements: 1.1, 11.6_
  
  - [ ] 7.6 Create recipient preview section
    - Show after parsing: total count, duplicate count
    - Display table with first 10 recipients
    - Show duplicate badge (🔄) for flagged recipients
    - _Requirements: 3.8, 8.5_
  
  - [ ] 7.7 Implement message template and text area
    - Reuse existing template dropdown from SPMB broadcast
    - Add textarea for message composition
    - Display available variables: {nama}, {phone}
    - Show warning if template contains SPMB-specific variables
    - _Requirements: 5.3, 5.4, 5.5, 9.1, 9.2, 9.3, 9.4, 9.5_
  
  - [ ] 7.8 Add send broadcast button
    - Large primary button to trigger broadcast
    - Disable if no recipients or WhatsApp disconnected
    - _Requirements: 5.6, 11.3_

- [ ] 8. Modify phone list Blade view for external tab
  - [ ] 8.1 Add "Eksternal" tab to phone-list.blade.php
    - Insert new tab after existing tabs with icon and count badge
    - Handle ?tab=external query parameter to set active state
    - _Requirements: 6.1_
  
  - [ ] 8.2 Create external recipients table section
    - Display when external tab is active
    - Show columns: Nama, Nomor HP, Batch, Pesan (view button), Terakhir
    - Add duplicate badge (🔄) with tooltip next to phone numbers flagged as duplicates
    - _Requirements: 6.2, 6.3_
  
  - [ ] 8.3 Add "Show duplicates only" filter checkbox
    - Place above table when external tab active
    - Filter results via JavaScript or page reload
    - _Requirements: 6.4_
  
  - [ ] 8.4 Implement view messages button and modal
    - Button to open modal showing message history for external recipient
    - Modal displays recipient details and WhatsAppLog entries
    - _Requirements: 6.5_
  
  - [ ] 8.5 Add link to SPMB pendaftar from duplicate badge
    - Clicking duplicate badge navigates to matching pendaftar record
    - _Requirements: 6.6_

- [ ] 9. Implement JavaScript for external broadcast UI
  - [ ] 9.1 Create externalBroadcast.js module
    - Handle source type radio button toggle (show/hide CSV vs manual sections)
    - Implement CSV file upload with validation (file type, size limit 2MB)
    - _Requirements: 3.1, 3.2_
  
  - [ ] 9.2 Implement CSV/manual input parsing AJAX call
    - Send FormData to /whatsapp/broadcast/external/parse endpoint
    - Display loading spinner during parsing
    - Handle validation errors and display to user with row numbers
    - _Requirements: 3.3, 3.4, 3.5, 4.4, 4.5, 4.6, 11.1, 11.2_
  
  - [ ] 9.3 Display recipient preview after parsing
    - Show preview section with total and duplicate counts
    - Render table with first 10 recipients
    - Highlight duplicates with badge and styling
    - _Requirements: 3.8, 8.5_
  
  - [ ] 9.4 Implement template variable replacement warnings
    - Detect SPMB-specific variables in template ({no_registrasi}, {jurusan}, {nisn})
    - Display warning alert if found
    - _Requirements: 9.3, 9.4_
  
  - [ ] 9.5 Implement send broadcast AJAX call
    - Validate batch has been parsed and recipients exist
    - Check WhatsApp connection status before sending
    - Send POST to /whatsapp/broadcast/external/send with batch_id and message
    - Display progress indication during broadcast
    - Show success/failure results with statistics
    - _Requirements: 5.6, 10.1, 10.2, 10.3, 10.4, 10.5, 11.3, 11.5, 15.4_
  
  - [ ]* 9.6 Write property test for message log integrity
    - **Property 4: Message Log Integrity**
    - **Validates: Requirements 7.2, 7.3, 7.4, 10.2, 10.3**
    - Test that every sent message has exactly one WhatsAppLog entry
    - Verify external_batch_id matches the originating batch
    - Verify log timestamps are monotonically increasing within a batch
  
  - [ ]* 9.7 Write property test for data consistency
    - **Property 5: Data Consistency**
    - **Validates: Requirements 10.5, 15.5**
    - Test that total_sent + total_failed equals number of WhatsAppLog entries for the batch
    - Verify recipient count in batch matches number of ExternalBroadcastRecipient records

- [ ] 10. Checkpoint - Integration testing and UI verification
  - Test full flow: upload CSV → parse → preview → send broadcast
  - Test manual input flow end-to-end
  - Verify duplicate detection displays correctly in UI
  - Test phone list external tab with various filters
  - Verify message history modal works for external recipients
  - Ensure all tests pass, ask the user if questions arise

- [ ] 11. Implement error handling and validation
  - [ ] 11.1 Add CSRF protection verification
    - Ensure all POST routes have CSRF token validation
    - Return 419 error with message if token invalid
    - _Requirements: 14.4_
  
  - [ ] 11.2 Add input sanitization for XSS prevention
    - Sanitize batch name, recipient names, notes, and message content
    - Use Laravel's e() helper or HTML Purifier for user inputs
    - _Requirements: 14.5_
  
  - [ ] 11.3 Implement rate limiting on broadcast endpoints
    - Add Laravel rate limiter to external broadcast routes (e.g., 10 requests per minute)
    - Return 429 Too Many Requests if exceeded
    - _Requirements: 15.3_
  
  - [ ] 11.4 Add database transaction rollback on errors
    - Wrap batch and recipient creation in DB::transaction()
    - Catch exceptions and rollback if any step fails
    - Log errors for debugging
    - _Requirements: 15.5_
  
  - [ ] 11.5 Implement broadcast progress tracking
    - Use session or cache to store broadcast progress
    - Provide endpoint to check progress status
    - Display progress bar or percentage in UI
    - _Requirements: 15.4_

- [ ] 12. Security and access control
  - [ ] 12.1 Verify middleware is applied to all external broadcast routes
    - Ensure CheckRole:admin_wa middleware protects all routes
    - Test unauthorized access returns 403
    - _Requirements: 14.1, 14.2_
  
  - [ ] 12.2 Add activity logging for external broadcasts
    - Log batch creation, parsing, and sending actions to UserActivityLog
    - Include user_id, action type, timestamp, and recipient count
    - _Requirements: 14.2_
  
  - [ ] 12.3 Validate file upload security
    - Check MIME type is text/csv
    - Store uploaded files in non-public directory
    - Delete temporary files after processing
    - _Requirements: 3.1, 14.3_
  
  - [ ] 12.4 Prevent SQL injection in queries
    - Use Eloquent ORM and query builder (already implemented)
    - Verify no raw SQL with user input
    - _Requirements: 14.3_

- [ ] 13. Performance optimization and testing
  - [ ] 13.1 Add database indexes
    - Verify indexes on external_broadcast_recipients(phone_normalized)
    - Verify indexes on whatsapp_logs(external_batch_id)
    - Run EXPLAIN on duplicate detection query to confirm index usage
    - _Requirements: 12.5, 12.6, 15.2_
  
  - [ ] 13.2 Test CSV upload with 1000 recipients
    - Upload CSV with 1000 rows and verify processing completes within 5 seconds
    - Check memory usage remains reasonable
    - _Requirements: 15.1_
  
  - [ ] 13.3 Test duplicate detection performance
    - Run duplicate detection on 500 phone numbers
    - Verify completion within 10 seconds
    - _Requirements: 8.7, 15.2_
  
  - [ ] 13.4 Implement pagination for phone list external tab
    - Ensure 20 recipients per page
    - Test pagination with large datasets (1000+ recipients)
    - _Requirements: 15.6_
  
  - [ ]* 13.5 Write performance tests
    - Test CSV processing time with 1000 recipients
    - Test duplicate detection with 500 numbers
    - Test broadcast sending with rate limiting (20 msg/min)
    - _Requirements: 15.1, 15.2, 15.3_

- [ ] 14. Final integration and deployment preparation
  - [ ] 14.1 Create database seeder for testing
    - Seed test external broadcast batches
    - Seed external recipients with mix of duplicate and non-duplicate entries
    - _Requirements: Testing support_
  
  - [ ] 14.2 Write comprehensive feature test
    - Test complete workflow: create batch → parse CSV → detect duplicates → send broadcast → verify logs
    - Test phone list external tab displays correct data
    - Test error cases: invalid CSV, WhatsApp offline, duplicate batch name
    - _Requirements: All requirements_
  
  - [ ] 14.3 Update documentation
    - Document new API endpoints and request/response formats
    - Document database schema changes
    - Create user guide for external broadcast feature
    - _Requirements: Documentation_
  
  - [ ] 14.4 Run all migrations on staging database
    - Execute migrations in order
    - Verify foreign key constraints are created
    - Verify indexes are created correctly
    - _Requirements: 12.1, 12.2, 12.3_
  
  - [ ] 14.5 Deploy to staging and conduct UAT
    - Deploy code changes to staging server
    - Test with real CSV data
    - Verify WhatsApp Gateway integration works
    - Collect feedback and fix any issues
    - _Requirements: All requirements_

- [ ] 15. Final checkpoint - Production readiness
  - All unit tests, integration tests, and feature tests pass
  - Performance benchmarks met (CSV processing, duplicate detection, broadcast rate)
  - Security checklist complete (authorization, CSRF, XSS, SQL injection)
  - Code review completed
  - Documentation updated
  - Staging UAT successful
  - Ensure all tests pass, ask the user if questions arise

## Notes

- **Optional Tasks**: Tasks marked with `*` are optional testing tasks that can be skipped for faster MVP delivery. However, they are highly recommended for production readiness.
- **Atomic Tasks**: Each task is designed to be independently testable and implementable.
- **Dependencies**: Tasks within each section build on previous tasks in that section. Cross-section dependencies are minimized.
- **Requirements Traceability**: Each task references specific requirements from the requirements document for full traceability.
- **Checkpoints**: Three checkpoints are included to ensure incremental validation and catch issues early.
- **Testing Strategy**: Property-based tests validate universal correctness properties, while integration tests verify end-to-end workflows.
- **Security**: Security tasks are integrated throughout rather than deferred to the end.
- **Performance**: Performance testing and optimization tasks ensure the feature meets scalability requirements.

## Task Dependency Graph

```json
{
  "waves": [
    {
      "id": 0,
      "tasks": ["1.1", "1.2", "1.3"]
    },
    {
      "id": 1,
      "tasks": ["2.1", "2.2", "2.3"]
    },
    {
      "id": 2,
      "tasks": ["3.1", "6.1", "6.2", "6.3", "6.4"]
    },
    {
      "id": 3,
      "tasks": ["3.2", "3.3", "3.4"]
    },
    {
      "id": 4,
      "tasks": ["3.5", "3.7"]
    },
    {
      "id": 5,
      "tasks": ["3.6", "3.8"]
    },
    {
      "id": 6,
      "tasks": ["3.9", "5.1"]
    },
    {
      "id": 7,
      "tasks": ["5.2", "5.4", "7.1"]
    },
    {
      "id": 8,
      "tasks": ["5.5", "7.2", "7.3", "7.4", "7.5", "8.1"]
    },
    {
      "id": 9,
      "tasks": ["7.6", "7.7", "8.2", "8.3", "9.1"]
    },
    {
      "id": 10,
      "tasks": ["5.3", "7.8", "8.4", "8.5", "9.2"]
    },
    {
      "id": 11,
      "tasks": ["5.6", "9.3", "9.4", "11.1", "11.2"]
    },
    {
      "id": 12,
      "tasks": ["9.5", "11.3", "11.4", "12.1", "12.2"]
    },
    {
      "id": 13,
      "tasks": ["9.6", "9.7", "11.5", "12.3", "12.4"]
    },
    {
      "id": 14,
      "tasks": ["13.1", "13.2", "13.3", "13.4"]
    },
    {
      "id": 15,
      "tasks": ["13.5", "14.1", "14.2"]
    },
    {
      "id": 16,
      "tasks": ["14.3", "14.4"]
    },
    {
      "id": 17,
      "tasks": ["14.5"]
    }
  ]
}
```
