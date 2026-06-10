# Requirements Document

## Introduction

The External Broadcast feature extends the existing SPMB WhatsApp Gateway to support broadcasting messages to recipients outside the SPMB database. This enables the system to send WhatsApp messages to alumni, manual contact lists, CSV uploads, and other external data sources while maintaining detailed tracking and duplicate detection with the existing SPMB student database.

## Glossary

- **SPMB_System**: The Student Registration Management and Broadcasting system
- **External_Broadcast**: Broadcast functionality for non-SPMB data sources
- **Broadcast_Batch**: A collection of external broadcast operations tracked as a unit
- **External_Recipient**: A person with contact information not from the SPMB database
- **SPMB_Database**: The database containing student registration data (pendaftar table)
- **Duplicate_Detection**: Process of identifying phone numbers that exist in both external data and SPMB database
- **WhatsApp_Gateway**: The service that sends WhatsApp messages
- **CSV_Parser**: Component that reads and validates CSV files
- **Phone_List**: UI component displaying broadcast history and recipient information
- **Broadcast_UI**: The user interface for creating and sending broadcasts

## Requirements

### Requirement 1: External Broadcast Batch Management

**User Story:** As an administrator, I want to create and track external broadcast batches, so that I can organize and monitor broadcasts sent to non-SPMB recipients.

#### Acceptance Criteria

1. WHEN an administrator initiates an external broadcast, THE SPMB_System SHALL create a Broadcast_Batch record with a unique identifier
2. THE Broadcast_Batch SHALL store the batch name, description, creation timestamp, total recipient count, and sending user
3. THE SPMB_System SHALL associate all external broadcast messages with their originating Broadcast_Batch
4. WHEN viewing broadcast history, THE SPMB_System SHALL display Broadcast_Batch information grouped by batch identifier
5. THE SPMB_System SHALL track the status of each Broadcast_Batch (pending, in_progress, completed, failed)

### Requirement 2: External Recipient Data Storage

**User Story:** As an administrator, I want to store external recipient data with duplicate detection, so that I can track which contacts exist in both external lists and the SPMB database.

#### Acceptance Criteria

1. WHEN an External_Recipient is added, THE SPMB_System SHALL store the name, phone number, optional notes, and associated Broadcast_Batch identifier
2. THE SPMB_System SHALL normalize phone numbers to a consistent format before storage (remove spaces, convert to 62xxx format)
3. WHEN storing an External_Recipient, THE Duplicate_Detection SHALL compare the phone number against all phone fields in the SPMB_Database (no_hp_wali, no_hp_ortu, no_telepon)
4. IF a phone number match is found, THEN THE SPMB_System SHALL set the is_duplicate_spmb flag to true for that External_Recipient
5. THE SPMB_System SHALL store duplicate detection results without blocking the broadcast operation
6. THE SPMB_System SHALL record the matching SPMB pendaftar_id when a duplicate is detected

### Requirement 3: CSV Upload and Parsing

**User Story:** As an administrator, I want to upload recipient lists via CSV files, so that I can efficiently broadcast to large external contact lists.

#### Acceptance Criteria

1. THE Broadcast_UI SHALL provide a CSV file upload interface accepting files up to 2MB in size
2. THE CSV_Parser SHALL accept CSV files with columns: name, phone, and optionally notes
3. WHEN a CSV file is uploaded, THE CSV_Parser SHALL validate that the file has a header row with required columns
4. THE CSV_Parser SHALL validate each row to ensure name and phone are not empty
5. IF a CSV row has invalid data, THEN THE CSV_Parser SHALL collect all validation errors and display them to the user with row numbers
6. WHEN CSV parsing succeeds, THE SPMB_System SHALL create External_Recipient records for all valid rows
7. THE CSV_Parser SHALL normalize phone numbers during parsing (remove hyphens, spaces, and parentheses)
8. THE SPMB_System SHALL provide a preview of the first 10 parsed records before confirming the upload

### Requirement 4: Manual Recipient Input

**User Story:** As an administrator, I want to manually enter recipient information via textarea, so that I can quickly broadcast to small external lists without creating CSV files.

#### Acceptance Criteria

1. THE Broadcast_UI SHALL provide a textarea input accepting phone numbers with one number per line
2. THE Broadcast_UI SHALL allow optional format: "phone|name|notes" separated by pipe character
3. WHEN manual input contains only phone numbers, THE SPMB_System SHALL use "External Contact" as the default name
4. THE SPMB_System SHALL validate that each line contains at least a valid phone number
5. THE SPMB_System SHALL parse up to 500 manual entries in a single submission
6. IF manual input validation fails, THEN THE SPMB_System SHALL display error messages indicating which lines are invalid

### Requirement 5: Broadcast UI Integration

**User Story:** As an administrator, I want to access external broadcast from the existing broadcast page, so that I have a unified interface for all broadcast operations.

#### Acceptance Criteria

1. THE Broadcast_UI SHALL display two tabs: "Data SPMB" and "Data Eksternal" on the broadcast page
2. WHEN "Data Eksternal" tab is selected, THE Broadcast_UI SHALL display CSV upload and manual input options
3. THE Broadcast_UI SHALL reuse the existing message template selection interface for external broadcasts
4. THE Broadcast_UI SHALL display recipient count and estimated send time for external broadcasts
5. THE Broadcast_UI SHALL support variable replacement for external recipients using {nama} for name and {phone} for phone number
6. WHEN sending an external broadcast, THE SPMB_System SHALL validate that at least one recipient exists before proceeding

### Requirement 6: Phone List Integration

**User Story:** As an administrator, I want to view external broadcast history in the Phone List, so that I can track all WhatsApp communications from one interface.

#### Acceptance Criteria

1. THE Phone_List SHALL display a new tab labeled "Eksternal" showing external broadcast recipients
2. THE Phone_List SHALL display External_Recipient information: name, phone number, batch name, message count, and last message date
3. WHEN an External_Recipient has is_duplicate_spmb flag set, THE Phone_List SHALL display a badge indicator (🔄) next to the phone number
4. THE Phone_List SHALL provide a filter option "Show duplicates only" to display only recipients flagged as duplicates
5. WHEN viewing an External_Recipient detail, THE Phone_List SHALL display all messages sent to that phone number
6. THE Phone_List SHALL allow clicking on duplicate badge to navigate to the matching SPMB pendaftar record

### Requirement 7: WhatsApp Gateway Integration

**User Story:** As an administrator, I want external broadcasts to use the existing WhatsApp Gateway service, so that all messages flow through a consistent, tested delivery system.

#### Acceptance Criteria

1. THE SPMB_System SHALL reuse the existing WhatsApp_Gateway service for sending external broadcast messages
2. WHEN sending to External_Recipient, THE SPMB_System SHALL create WhatsAppLog entries with type "external_broadcast"
3. THE SPMB_System SHALL populate the external_batch_id field in WhatsAppLog for external broadcast messages
4. THE SPMB_System SHALL set pendaftar_id to null in WhatsAppLog for recipients not in SPMB_Database
5. IF an External_Recipient is detected as duplicate, THE SPMB_System SHALL populate pendaftar_id with the matching SPMB record
6. THE SPMB_System SHALL apply the same rate limiting (1 second delay between messages) to external broadcasts
7. THE SPMB_System SHALL track send status (sent, failed, pending) identically for both SPMB and external broadcasts

### Requirement 8: Duplicate Detection Algorithm

**User Story:** As an administrator, I want automatic duplicate detection during external broadcast, so that I can identify contacts that overlap with the SPMB database.

#### Acceptance Criteria

1. THE Duplicate_Detection SHALL normalize phone numbers to 62xxx format before comparison
2. THE Duplicate_Detection SHALL search the SPMB_Database for matches in fields: no_hp_wali, no_hp_ortu, and no_telepon
3. THE Duplicate_Detection SHALL perform case-insensitive comparison after removing all non-numeric characters
4. WHEN multiple SPMB records match the same phone number, THE Duplicate_Detection SHALL flag the first match
5. THE Duplicate_Detection SHALL execute during External_Recipient creation before message sending
6. THE Duplicate_Detection SHALL NOT prevent message sending when duplicates are found
7. THE Duplicate_Detection SHALL complete within 100ms per phone number for batch operations

### Requirement 9: Message Template Compatibility

**User Story:** As an administrator, I want to use existing message templates with external broadcasts, so that I can maintain consistent messaging across all recipient types.

#### Acceptance Criteria

1. THE SPMB_System SHALL make all active WhatsApp templates available for external broadcasts
2. WHEN using a template with External_Recipient, THE SPMB_System SHALL replace {nama} variable with the recipient name
3. WHEN a template contains SPMB-specific variables (no_registrasi, jurusan, nisn), THE SPMB_System SHALL replace them with empty string or default text
4. THE Broadcast_UI SHALL display a warning when templates contain SPMB-specific variables for external broadcasts
5. THE SPMB_System SHALL support custom variable definition for external broadcasts: {phone} and {notes}

### Requirement 10: Broadcast Execution and Logging

**User Story:** As an administrator, I want detailed logging of external broadcasts, so that I can audit message delivery and troubleshoot failures.

#### Acceptance Criteria

1. WHEN an external broadcast begins, THE SPMB_System SHALL log the batch start time, user, and recipient count
2. THE SPMB_System SHALL create individual WhatsAppLog entries for each message in the external broadcast
3. THE SPMB_System SHALL record send timestamp, delivery status, and error messages for each External_Recipient
4. WHEN a broadcast completes, THE SPMB_System SHALL update the Broadcast_Batch status and record completion time
5. THE SPMB_System SHALL calculate and display success rate (sent/total) for each Broadcast_Batch
6. THE SPMB_System SHALL preserve External_Recipient records and associated logs for at least 90 days

### Requirement 11: Error Handling and Validation

**User Story:** As an administrator, I want clear error messages during external broadcast operations, so that I can correct issues and successfully send messages.

#### Acceptance Criteria

1. IF CSV upload fails validation, THEN THE SPMB_System SHALL display specific error messages with row and column information
2. IF manual input contains invalid phone numbers, THEN THE SPMB_System SHALL highlight invalid lines with explanatory messages
3. IF WhatsApp_Gateway is disconnected, THEN THE SPMB_System SHALL prevent external broadcast submission and display connection status
4. IF duplicate phone numbers exist within the same external broadcast batch, THEN THE SPMB_System SHALL deduplicate and notify the user
5. WHEN a broadcast message fails to send, THE SPMB_System SHALL record the error message and allow manual retry
6. THE SPMB_System SHALL validate that batch name is unique within the last 30 days

### Requirement 12: Database Schema Integration

**User Story:** As a system architect, I want external broadcast tables to integrate cleanly with existing schema, so that the system remains maintainable and performant.

#### Acceptance Criteria

1. THE SPMB_System SHALL add external_batch_id column (nullable integer) to the whatsapp_logs table
2. THE SPMB_System SHALL create external_broadcast_batches table with columns: id, batch_name, description, total_recipients, status, created_by, created_at, completed_at
3. THE SPMB_System SHALL create external_broadcast_recipients table with columns: id, batch_id, name, phone, notes, is_duplicate_spmb, matched_pendaftar_id, created_at
4. THE SPMB_System SHALL create foreign key constraints between external_batch_id and external_broadcast_batches(id)
5. THE SPMB_System SHALL create index on external_broadcast_recipients(phone) for duplicate detection performance
6. THE SPMB_System SHALL create index on whatsapp_logs(external_batch_id) for batch filtering performance

### Requirement 13: Phone Number Normalization

**User Story:** As a system component, I want consistent phone number formatting, so that duplicate detection and message sending work reliably.

#### Acceptance Criteria

1. THE SPMB_System SHALL normalize phone numbers by removing characters: space, hyphen, parentheses, and plus sign
2. WHEN a phone number starts with "0", THE SPMB_System SHALL convert it to "62" prefix format
3. WHEN a phone number starts with "8", THE SPMB_System SHALL prepend "62" to create valid Indonesian format
4. THE SPMB_System SHALL reject phone numbers shorter than 10 digits or longer than 15 digits after normalization
5. THE SPMB_System SHALL store both the original input and normalized format for External_Recipient records
6. WHEN sending to WhatsApp_Gateway, THE SPMB_System SHALL use the normalized phone format

### Requirement 14: Security and Access Control

**User Story:** As a system administrator, I want external broadcast features restricted to authorized users, so that sensitive contact data and messaging capabilities are protected.

#### Acceptance Criteria

1. THE SPMB_System SHALL restrict external broadcast access to users with "admin_wa" role or higher
2. THE SPMB_System SHALL log all external broadcast operations including user ID, timestamp, and recipient count
3. THE SPMB_System SHALL prevent direct database access to external_broadcast_recipients from public routes
4. THE SPMB_System SHALL validate CSRF tokens on all external broadcast form submissions
5. THE SPMB_System SHALL sanitize all user input (names, notes, batch descriptions) to prevent XSS attacks

### Requirement 15: Performance and Scalability

**User Story:** As a system architect, I want external broadcast to handle hundreds of recipients efficiently, so that the system remains responsive during broadcast operations.

#### Acceptance Criteria

1. THE SPMB_System SHALL process CSV uploads with up to 1000 recipients within 5 seconds
2. THE Duplicate_Detection SHALL complete batch processing of 500 phone numbers within 10 seconds
3. THE SPMB_System SHALL send external broadcast messages at a rate of 20 messages per minute maximum
4. THE Broadcast_UI SHALL display progress indication during long-running broadcast operations
5. THE SPMB_System SHALL use database transactions to ensure atomic creation of Broadcast_Batch and External_Recipient records
6. THE SPMB_System SHALL paginate Phone_List external tab results with 20 recipients per page
