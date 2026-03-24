<?php

declare(strict_types=1);

/**
 * Default Roles and Permissions Configuration
 *
 * Each role maps to an array of permission slugs.
 * Permissions follow the pattern: module.action
 */

return [
    'roles' => [
        'super_admin' => [
            'name' => 'Super Admin',
            'description' => 'Platform owner with full system access',
            'is_system' => true,
        ],
        'org_admin' => [
            'name' => 'Organization Admin',
            'description' => 'Full access to organization resources',
            'is_system' => true,
        ],
        'facility_admin' => [
            'name' => 'Facility Admin',
            'description' => 'Full access to assigned facility',
            'is_system' => true,
        ],
        'manager' => [
            'name' => 'Manager',
            'description' => 'Can manage daily operations',
            'is_system' => true,
        ],
        'coach' => [
            'name' => 'Coach',
            'description' => 'Can manage sessions and players',
            'is_system' => true,
        ],
        'staff' => [
            'name' => 'Staff',
            'description' => 'Basic operational access',
            'is_system' => true,
        ],
        'player' => [
            'name' => 'Player',
            'description' => 'Player/member access',
            'is_system' => true,
        ],
    ],

    'permissions' => [
        // Organizations
        'organizations.view' => ['module' => 'organizations', 'description' => 'View organizations'],
        'organizations.create' => ['module' => 'organizations', 'description' => 'Create organizations'],
        'organizations.update' => ['module' => 'organizations', 'description' => 'Update organizations'],
        'organizations.delete' => ['module' => 'organizations', 'description' => 'Delete organizations'],

        // Facilities
        'facilities.view' => ['module' => 'facilities', 'description' => 'View facilities'],
        'facilities.create' => ['module' => 'facilities', 'description' => 'Create facilities'],
        'facilities.update' => ['module' => 'facilities', 'description' => 'Update facilities'],
        'facilities.delete' => ['module' => 'facilities', 'description' => 'Delete facilities'],

        // Courts
        'courts.view' => ['module' => 'courts', 'description' => 'View courts'],
        'courts.create' => ['module' => 'courts', 'description' => 'Create courts'],
        'courts.update' => ['module' => 'courts', 'description' => 'Update courts'],
        'courts.delete' => ['module' => 'courts', 'description' => 'Delete courts'],

        // Users
        'users.view' => ['module' => 'users', 'description' => 'View users'],
        'users.create' => ['module' => 'users', 'description' => 'Create users'],
        'users.update' => ['module' => 'users', 'description' => 'Update users'],
        'users.delete' => ['module' => 'users', 'description' => 'Delete users'],

        // Roles
        'roles.view' => ['module' => 'roles', 'description' => 'View roles'],
        'roles.create' => ['module' => 'roles', 'description' => 'Create roles'],
        'roles.update' => ['module' => 'roles', 'description' => 'Update roles'],
        'roles.delete' => ['module' => 'roles', 'description' => 'Delete roles'],
        'roles.assign' => ['module' => 'roles', 'description' => 'Assign roles to users'],

        // Players
        'players.view' => ['module' => 'players', 'description' => 'View players'],
        'players.create' => ['module' => 'players', 'description' => 'Create players'],
        'players.update' => ['module' => 'players', 'description' => 'Update players'],
        'players.delete' => ['module' => 'players', 'description' => 'Delete players'],

        // Staff
        'staff.view' => ['module' => 'staff', 'description' => 'View staff'],
        'staff.create' => ['module' => 'staff', 'description' => 'Create staff'],
        'staff.update' => ['module' => 'staff', 'description' => 'Update staff'],
        'staff.delete' => ['module' => 'staff', 'description' => 'Delete staff'],

        // Subscriptions
        'subscriptions.view' => ['module' => 'subscriptions', 'description' => 'View subscriptions'],
        'subscriptions.manage' => ['module' => 'subscriptions', 'description' => 'Manage subscriptions'],

        // Payments
        'payments.view' => ['module' => 'payments', 'description' => 'View payments'],
        'payments.process' => ['module' => 'payments', 'description' => 'Process payments'],
        'payments.refund' => ['module' => 'payments', 'description' => 'Refund payments'],

        // Notifications
        'notifications.view' => ['module' => 'notifications', 'description' => 'View notifications'],
        'notifications.manage' => ['module' => 'notifications', 'description' => 'Manage notifications'],

        // Files
        'files.view' => ['module' => 'files', 'description' => 'View files'],
        'files.upload' => ['module' => 'files', 'description' => 'Upload files'],
        'files.delete' => ['module' => 'files', 'description' => 'Delete files'],

        // Settings
        'settings.view' => ['module' => 'settings', 'description' => 'View settings'],
        'settings.update' => ['module' => 'settings', 'description' => 'Update settings'],

        // Audit Logs
        'audit_logs.view' => ['module' => 'audit_logs', 'description' => 'View audit logs'],

        // API Tokens
        'api_tokens.view' => ['module' => 'api_tokens', 'description' => 'View API tokens'],
        'api_tokens.create' => ['module' => 'api_tokens', 'description' => 'Create API tokens'],
        'api_tokens.revoke' => ['module' => 'api_tokens', 'description' => 'Revoke API tokens'],

        // Platform (super admin only)
        'platform.manage' => ['module' => 'platform', 'description' => 'Manage platform settings'],
        'platform.analytics' => ['module' => 'platform', 'description' => 'View platform analytics'],

        // Schedule / Calendar
        'schedule.view' => ['module' => 'schedule', 'description' => 'View schedule and calendar'],

        // Session Types
        'session_types.view' => ['module' => 'session_types', 'description' => 'View session types and classes'],
        'session_types.create' => ['module' => 'session_types', 'description' => 'Create session types and classes'],
        'session_types.update' => ['module' => 'session_types', 'description' => 'Update session types, classes and attendees'],
        'session_types.delete' => ['module' => 'session_types', 'description' => 'Delete session types and classes'],

        // Session Details
        'session_details.view' => ['module' => 'session_details', 'description' => 'View session details'],
        'session_details.create' => ['module' => 'session_details', 'description' => 'Create session details'],
        'session_details.update' => ['module' => 'session_details', 'description' => 'Update session details'],
        'session_details.delete' => ['module' => 'session_details', 'description' => 'Delete session details'],

        // Resources
        'resources.view' => ['module' => 'resources', 'description' => 'View resources'],
        'resources.create' => ['module' => 'resources', 'description' => 'Create resources'],
        'resources.update' => ['module' => 'resources', 'description' => 'Update resources'],
        'resources.delete' => ['module' => 'resources', 'description' => 'Delete resources'],

        // Categories
        'categories.view' => ['module' => 'categories', 'description' => 'View categories'],
        'categories.create' => ['module' => 'categories', 'description' => 'Create categories'],
        'categories.update' => ['module' => 'categories', 'description' => 'Update categories'],
        'categories.delete' => ['module' => 'categories', 'description' => 'Delete categories'],

        // Labels
        'labels.view' => ['module' => 'labels', 'description' => 'View labels'],
        'labels.create' => ['module' => 'labels', 'description' => 'Create labels'],
        'labels.update' => ['module' => 'labels', 'description' => 'Update labels'],
        'labels.delete' => ['module' => 'labels', 'description' => 'Delete labels'],

        // Waivers
        'waivers.view' => ['module' => 'waivers', 'description' => 'View waivers'],
        'waivers.create' => ['module' => 'waivers', 'description' => 'Create waivers'],
        'waivers.update' => ['module' => 'waivers', 'description' => 'Update and activate waivers'],
        'waivers.delete' => ['module' => 'waivers', 'description' => 'Delete waivers'],

        // Discounts
        'discounts.view' => ['module' => 'discounts', 'description' => 'View discounts'],
        'discounts.create' => ['module' => 'discounts', 'description' => 'Create discounts'],
        'discounts.update' => ['module' => 'discounts', 'description' => 'Update discounts'],
        'discounts.delete' => ['module' => 'discounts', 'description' => 'Delete discounts'],

        // Credit Codes
        'credit_codes.view' => ['module' => 'credit_codes', 'description' => 'View credit codes'],
        'credit_codes.create' => ['module' => 'credit_codes', 'description' => 'Create credit codes'],
        'credit_codes.update' => ['module' => 'credit_codes', 'description' => 'Update credit codes'],
        'credit_codes.delete' => ['module' => 'credit_codes', 'description' => 'Delete credit codes'],

        // Gift Certificates
        'gift_certificates.view' => ['module' => 'gift_certificates', 'description' => 'View gift certificates'],
        'gift_certificates.create' => ['module' => 'gift_certificates', 'description' => 'Create gift certificates'],
        'gift_certificates.update' => ['module' => 'gift_certificates', 'description' => 'Update gift certificates'],
        'gift_certificates.delete' => ['module' => 'gift_certificates', 'description' => 'Delete gift certificates'],

        // Extensions
        'extensions.view' => ['module' => 'extensions', 'description' => 'View available extensions'],
        'extensions.install' => ['module' => 'extensions', 'description' => 'Install and uninstall extensions'],
        'extensions.configure' => ['module' => 'extensions', 'description' => 'Configure extension settings'],
    ],

    // Role → Permission mapping
    'role_permissions' => [
        'super_admin' => ['*'], // All permissions

        'org_admin' => [
            'organizations.view', 'organizations.update',
            'facilities.*', 'courts.*', 'users.*', 'roles.*',
            'players.*', 'staff.*',
            'subscriptions.*', 'payments.*',
            'notifications.*', 'files.*',
            'settings.*', 'audit_logs.view',
            'api_tokens.*',
            // Scheduling & sessions
            'schedule.view', 'session_types.*', 'session_details.*', 'resources.*',
            // Content management
            'categories.*', 'labels.*', 'waivers.*',
            // Promotions & payments
            'discounts.*', 'credit_codes.*', 'gift_certificates.*',
            // Extensions
            'extensions.*',
        ],

        'facility_admin' => [
            'facilities.view', 'facilities.update',
            'courts.*',
            'users.view', 'users.create', 'users.update',
            'players.*', 'staff.*',
            'payments.view', 'payments.process',
            'notifications.view',
            'files.view', 'files.upload',
            'settings.view',
            // Scheduling & sessions
            'schedule.view', 'session_types.*', 'session_details.*', 'resources.*',
            // Content management
            'categories.*', 'labels.*', 'waivers.*',
            // Promotions
            'discounts.*', 'credit_codes.*', 'gift_certificates.*',
            // Extensions (view only)
            'extensions.view', 'extensions.configure',
        ],

        'manager' => [
            'facilities.view',
            'courts.view', 'courts.update',
            'users.view',
            'players.*', 'staff.view',
            'payments.view',
            'notifications.view',
            'files.view', 'files.upload',
            // Scheduling & sessions (view + update for attendee management)
            'schedule.view', 'session_types.view', 'session_types.update',
            'session_details.view', 'resources.view',
            // Content (view)
            'categories.view', 'labels.view', 'waivers.view',
            'discounts.view', 'credit_codes.view', 'gift_certificates.view',
        ],

        'coach' => [
            'facilities.view',
            'courts.view',
            'players.view', 'players.update',
            'notifications.view',
            'files.view',
            // Scheduling (view only)
            'schedule.view', 'session_types.view', 'session_details.view',
        ],

        'staff' => [
            'facilities.view',
            'courts.view',
            'players.view',
            'notifications.view',
            // Schedule view
            'schedule.view', 'session_types.view',
        ],

        'player' => [
            'notifications.view',
            'files.view',
        ],
    ],
];
