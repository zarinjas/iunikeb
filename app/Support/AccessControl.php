<?php

namespace App\Support;

class AccessControl
{
    public const ROLE_SUPER_ADMIN = 'super_admin';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_MEMBER = 'member';

    public const PERMISSION_VIEW_ADMIN_DASHBOARD = 'view_admin_dashboard';

    public const PERMISSION_EDIT_FRONTPAGE = 'edit_frontpage';

    public const PERMISSION_VIEW_MEDIA = 'view_media';

    public const PERMISSION_UPLOAD_MEDIA = 'upload_media';

    public const PERMISSION_DELETE_MEDIA = 'delete_media';

    public const PERMISSION_VIEW_MEMBERS = 'view_members';

    public const PERMISSION_CREATE_MEMBERS = 'create_members';

    public const PERMISSION_EDIT_MEMBERS = 'edit_members';

    public const PERMISSION_SUSPEND_MEMBERS = 'suspend_members';

    public const PERMISSION_DELETE_MEMBERS = 'delete_members';

    public const PERMISSION_VIEW_MEMBERSHIP_APPLICATIONS = 'view_membership_applications';

    public const PERMISSION_REVIEW_MEMBERSHIP_APPLICATIONS = 'review_membership_applications';

    public const PERMISSION_APPROVE_MEMBERSHIP_APPLICATIONS = 'approve_membership_applications';

    public const PERMISSION_REJECT_MEMBERSHIP_APPLICATIONS = 'reject_membership_applications';

    public const PERMISSION_VIEW_COMPLAINTS = 'view_complaints';

    public const PERMISSION_REPLY_COMPLAINTS = 'reply_complaints';

    public const PERMISSION_CLOSE_COMPLAINTS = 'close_complaints';

    public const PERMISSION_VIEW_USERS = 'view_users';

    public const PERMISSION_VIEW_ROLES = 'view_roles';

    public const PERMISSION_VIEW_SETTINGS = 'view_settings';

    public const PERMISSION_EDIT_SETTINGS = 'edit_settings';

    public const PERMISSION_VIEW_AUDIT_LOGS = 'view_audit_logs';

    public const PERMISSION_VIEW_REPORTS = 'view_reports';

    public const PERMISSION_VIEW_FORMS = 'view_forms';

    public const PERMISSION_CREATE_FORMS = 'create_forms';

    public const PERMISSION_EDIT_FORMS = 'edit_forms';

    public const PERMISSION_DELETE_FORMS = 'delete_forms';

    public const PERMISSION_PUBLISH_FORMS = 'publish_forms';

    public const PERMISSION_VIEW_FORM_SUBMISSIONS = 'view_form_submissions';

    public const PERMISSION_VIEW_PROGRAMS = 'view_programs';

    public const PERMISSION_CREATE_PROGRAMS = 'create_programs';

    public const PERMISSION_EDIT_PROGRAMS = 'edit_programs';

    public const PERMISSION_DELETE_PROGRAMS = 'delete_programs';

    public const PERMISSION_PUBLISH_PROGRAMS = 'publish_programs';

    public const PERMISSION_SCAN_ATTENDANCE = 'scan_attendance';

    public const PERMISSION_VIEW_ATTENDANCE_REPORTS = 'view_attendance_reports';

    public const PERMISSION_MEMBER_ACCESS = 'member_access';

    public const PERMISSION_MANAGE_UNITS = 'manage_units';

    public const PERMISSION_MANAGE_STAFF = 'manage_staff';

    public const PERMISSION_VIEW_FINANCING = 'view_financing';

    public const PERMISSION_MANAGE_FINANCING_CATEGORIES = 'manage_financing_categories';

    public const PERMISSION_MANAGE_FINANCING_PRODUCTS = 'manage_financing_products';

    public const PERMISSION_REVIEW_FINANCING_APPLICATIONS = 'review_financing_applications';

    public const PERMISSION_APPROVE_FINANCING_APPLICATIONS = 'approve_financing_applications';

    public const PERMISSION_VIEW_ANSURAN = 'view_ansuran';

    public const PERMISSION_MANAGE_ANSURAN_PRODUCTS = 'manage_ansuran_products';

    public const PERMISSION_MANAGE_ANSURAN_TENURES = 'manage_ansuran_tenures';

    public const PERMISSION_MANAGE_ANSURAN_TEMPLATES = 'manage_ansuran_templates';

    public const PERMISSION_REVIEW_ANSURAN_APPLICATIONS = 'review_ansuran_applications';

    public const PERMISSION_APPROVE_ANSURAN_APPLICATIONS = 'approve_ansuran_applications';

    public const PERMISSION_VIEW_REFERRAL_COMMISSIONS = 'view_referral_commissions';

    public const PERMISSION_PROCESS_REFERRAL_PAYMENTS = 'process_referral_payments';

    public const PERMISSION_EDIT_MEMBER_FINANCIALS = 'edit_member_financials';

    public const PERMISSION_VIEW_CARUMAN = 'view_caruman';

    public const PERMISSION_EDIT_CARUMAN = 'edit_caruman';

    public const PERMISSION_VIEW_AI_KNOWLEDGE = 'view_ai_knowledge';

    public const PERMISSION_MANAGE_AI_KNOWLEDGE = 'manage_ai_knowledge';

    public const PERMISSION_MANAGE_POSTERS = 'manage_posters';

    public static function roles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
            self::ROLE_MEMBER,
        ];
    }

    public static function permissions(): array
    {
        return [
            self::PERMISSION_VIEW_ADMIN_DASHBOARD,
            self::PERMISSION_EDIT_FRONTPAGE,
            self::PERMISSION_VIEW_MEDIA,
            self::PERMISSION_UPLOAD_MEDIA,
            self::PERMISSION_DELETE_MEDIA,
            self::PERMISSION_VIEW_MEMBERS,
            self::PERMISSION_CREATE_MEMBERS,
            self::PERMISSION_EDIT_MEMBERS,
            self::PERMISSION_SUSPEND_MEMBERS,
            self::PERMISSION_DELETE_MEMBERS,
            self::PERMISSION_VIEW_MEMBERSHIP_APPLICATIONS,
            self::PERMISSION_REVIEW_MEMBERSHIP_APPLICATIONS,
            self::PERMISSION_APPROVE_MEMBERSHIP_APPLICATIONS,
            self::PERMISSION_REJECT_MEMBERSHIP_APPLICATIONS,
            self::PERMISSION_VIEW_COMPLAINTS,
            self::PERMISSION_REPLY_COMPLAINTS,
            self::PERMISSION_CLOSE_COMPLAINTS,
            self::PERMISSION_VIEW_USERS,
            self::PERMISSION_VIEW_ROLES,
            self::PERMISSION_VIEW_SETTINGS,
            self::PERMISSION_EDIT_SETTINGS,
            self::PERMISSION_VIEW_AUDIT_LOGS,
            self::PERMISSION_VIEW_REPORTS,
            self::PERMISSION_VIEW_FORMS,
            self::PERMISSION_CREATE_FORMS,
            self::PERMISSION_EDIT_FORMS,
            self::PERMISSION_DELETE_FORMS,
            self::PERMISSION_PUBLISH_FORMS,
            self::PERMISSION_VIEW_FORM_SUBMISSIONS,
            self::PERMISSION_VIEW_PROGRAMS,
            self::PERMISSION_CREATE_PROGRAMS,
            self::PERMISSION_EDIT_PROGRAMS,
            self::PERMISSION_DELETE_PROGRAMS,
            self::PERMISSION_PUBLISH_PROGRAMS,
            self::PERMISSION_SCAN_ATTENDANCE,
            self::PERMISSION_VIEW_ATTENDANCE_REPORTS,
            self::PERMISSION_MEMBER_ACCESS,
            self::PERMISSION_MANAGE_UNITS,
            self::PERMISSION_MANAGE_STAFF,
            self::PERMISSION_VIEW_FINANCING,
            self::PERMISSION_MANAGE_FINANCING_CATEGORIES,
            self::PERMISSION_MANAGE_FINANCING_PRODUCTS,
            self::PERMISSION_REVIEW_FINANCING_APPLICATIONS,
            self::PERMISSION_APPROVE_FINANCING_APPLICATIONS,
            self::PERMISSION_VIEW_ANSURAN,
            self::PERMISSION_MANAGE_ANSURAN_PRODUCTS,
            self::PERMISSION_MANAGE_ANSURAN_TENURES,
            self::PERMISSION_MANAGE_ANSURAN_TEMPLATES,
            self::PERMISSION_REVIEW_ANSURAN_APPLICATIONS,
            self::PERMISSION_APPROVE_ANSURAN_APPLICATIONS,
            self::PERMISSION_VIEW_REFERRAL_COMMISSIONS,
            self::PERMISSION_PROCESS_REFERRAL_PAYMENTS,
            self::PERMISSION_EDIT_MEMBER_FINANCIALS,
            self::PERMISSION_VIEW_CARUMAN,
            self::PERMISSION_EDIT_CARUMAN,
            self::PERMISSION_VIEW_AI_KNOWLEDGE,
            self::PERMISSION_MANAGE_AI_KNOWLEDGE,
            self::PERMISSION_MANAGE_POSTERS,
        ];
    }

    public static function rolePermissions(): array
    {
        $adminDashboard = [self::PERMISSION_VIEW_ADMIN_DASHBOARD];

        return [
            self::ROLE_SUPER_ADMIN => self::permissions(),
            self::ROLE_ADMIN => [
                ...$adminDashboard,
                self::PERMISSION_EDIT_FRONTPAGE,
                self::PERMISSION_VIEW_MEDIA,
                self::PERMISSION_UPLOAD_MEDIA,
                self::PERMISSION_DELETE_MEDIA,
                self::PERMISSION_VIEW_MEMBERS,
                self::PERMISSION_CREATE_MEMBERS,
                self::PERMISSION_EDIT_MEMBERS,
                self::PERMISSION_SUSPEND_MEMBERS,
                self::PERMISSION_VIEW_MEMBERSHIP_APPLICATIONS,
                self::PERMISSION_REVIEW_MEMBERSHIP_APPLICATIONS,
                self::PERMISSION_APPROVE_MEMBERSHIP_APPLICATIONS,
                self::PERMISSION_REJECT_MEMBERSHIP_APPLICATIONS,
                self::PERMISSION_VIEW_COMPLAINTS,
                self::PERMISSION_REPLY_COMPLAINTS,
                self::PERMISSION_CLOSE_COMPLAINTS,
                self::PERMISSION_VIEW_USERS,
                self::PERMISSION_VIEW_SETTINGS,
                self::PERMISSION_EDIT_SETTINGS,
                self::PERMISSION_VIEW_AUDIT_LOGS,
                self::PERMISSION_VIEW_REPORTS,
                self::PERMISSION_VIEW_FORMS,
                self::PERMISSION_CREATE_FORMS,
                self::PERMISSION_EDIT_FORMS,
                self::PERMISSION_DELETE_FORMS,
                self::PERMISSION_PUBLISH_FORMS,
                self::PERMISSION_VIEW_FORM_SUBMISSIONS,
                self::PERMISSION_VIEW_PROGRAMS,
                self::PERMISSION_CREATE_PROGRAMS,
                self::PERMISSION_EDIT_PROGRAMS,
                self::PERMISSION_DELETE_PROGRAMS,
                self::PERMISSION_PUBLISH_PROGRAMS,
                self::PERMISSION_SCAN_ATTENDANCE,
                self::PERMISSION_VIEW_ATTENDANCE_REPORTS,
                self::PERMISSION_VIEW_FINANCING,
                self::PERMISSION_MANAGE_FINANCING_CATEGORIES,
                self::PERMISSION_MANAGE_FINANCING_PRODUCTS,
                self::PERMISSION_REVIEW_FINANCING_APPLICATIONS,
                self::PERMISSION_APPROVE_FINANCING_APPLICATIONS,
                self::PERMISSION_VIEW_ANSURAN,
                self::PERMISSION_MANAGE_ANSURAN_PRODUCTS,
                self::PERMISSION_MANAGE_ANSURAN_TENURES,
                self::PERMISSION_MANAGE_ANSURAN_TEMPLATES,
                self::PERMISSION_REVIEW_ANSURAN_APPLICATIONS,
                self::PERMISSION_APPROVE_ANSURAN_APPLICATIONS,
                self::PERMISSION_VIEW_REFERRAL_COMMISSIONS,
                self::PERMISSION_PROCESS_REFERRAL_PAYMENTS,
                self::PERMISSION_EDIT_MEMBER_FINANCIALS,
                self::PERMISSION_VIEW_CARUMAN,
                self::PERMISSION_EDIT_CARUMAN,
                self::PERMISSION_VIEW_AI_KNOWLEDGE,
                self::PERMISSION_MANAGE_AI_KNOWLEDGE,
                self::PERMISSION_MANAGE_POSTERS,
            ],
            self::ROLE_MEMBER => [
                self::PERMISSION_MEMBER_ACCESS,
            ],
        ];
    }

    public static function adminRoles(): array
    {
        return [
            self::ROLE_SUPER_ADMIN,
            self::ROLE_ADMIN,
        ];
    }
}
