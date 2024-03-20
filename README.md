# Issue Tracking System

This module used to create an issue tracking system with the following features:

1. Programmatically creates the Issue content type with specific fields.
2. Programmatically creates vocabularies for "Issue type", "Priority", and "Status".
3. Programmatically creates a custom block to display the latest assigned issues for the current user.

## Installation

1. Download the custom module and place it in the `modules/custom` directory of your Drupal installation.
2. Enable the module in the Drupal administration interface (`/admin/modules`).

## Configuration

1. After Installation Create 5 to 10 content with Issue Content Type for testing the module. Add different assignee in every 3rd content.
2. Place custom block with name 'Latest Assigned Issues Block' in a reigon (`/admin/structure/block`).
3. Also create 5 to 10 users for testing.
4. Then test the 'Latest Assigned Issues Block' with different users.

## Credits/Maintainer

This module is developed by Priya Degwekar for assignment purpose.
