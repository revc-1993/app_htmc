export const OPERATIONS = {
    CREATE: 1,
    SHOW: 2,
    UPDATE: 3,
    DELETE: 4,
};

export const STATUSES = {
    PENDING_REVIEW: 1,
    REVIEWING: 2,
    OBSERVED: 3,
    RETURNED: 4,
    REGISTERED: 5,
    APPROVED: 6,
    CANCELED: 7,
    LIQUIDATED: 8,
};

export const STEPS = {
    STEP_1: 1,
    STEP_2: 2,
    STEP_3: 3,
    STEP_4: 4,
    ALL: 5,
};

export const ROLES = {
    ADMIN: "admin_role",
    COORD: "esgisef_approver_role",
};

export const ANALYST_STATUSES = {
    PENDING_REVIEW: 1,
    REVIEWING: 2,
    REVIEWED: 3,
    RETURNED: 4,
};

export const MANAGER_STATUSES = {
    PENDING_REVIEW: 1,
    REVIEWING: 2,
    REVIEWED: 3,
    RETURNED: 4,
    PAID: 5,
};
