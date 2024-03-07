import {
    mdiAccount,
    mdiMonitor,
    mdiShieldCrown,
    mdiTextBoxMultipleOutline,
    mdiCash,
    mdiWallet,
    mdiBriefcase,
    mdiBook,
    mdiCreditCardMarker,
    mdiAccountTie,
    mdiFolderAccount,
    mdiCheckbook,
    mdiAccountGroup,
    mdiBadgeAccountHorizontal,
    mdiCogs,
} from "@mdi/js";

export default [
    {
        route: "dashboard",
        icon: mdiMonitor,
        label: "Dashboard",
    },
    {
        route: "certifications.index",
        label: "Certificaciones",
        icon: mdiBook,
    },
    {
        route: "commitments.index",
        label: "Compromisos",
        icon: mdiCreditCardMarker,
    },
    {
        route: "accruals.index",
        label: "Devengados",
        icon: mdiWallet,
    },
    {
        route: "payments.index",
        label: "Tesorería",
        icon: mdiCash,
    },
    // {
    //     to: "accruals.index",
    //     label: "Anticipos",
    //     icon: mdiRefresh,
    // },
    // {
    //     to: "accruals.index",
    //     label: "Pagos",
    //     icon: mdiCash,
    // },
    // {
    //     to: "/forms",
    //     label: "Forms",
    //     icon: mdiSquareEditOutline,
    // },
    // {
    //     to: "/ui",
    //     label: "UI",
    //     icon: mdiTelevisionGuide,
    // },
    // {
    //     to: "/responsive",
    //     label: "Responsive",
    //     icon: mdiResponsive,
    // },
    // {
    //     to: "/",
    //     label: "Styles",
    //     icon: mdiPalette,
    // },
    {
        to: "/profile",
        label: "Profile",
        icon: mdiAccount,
    },
    {
        label: "Administrador",
        icon: mdiCogs,
        menu: [
            {
                route: "superadmin.users.index",
                label: "Usuarios",
                icon: mdiAccountGroup,
            },
            {
                // route: "",
                route: "superadmin.departments.index",
                label: "Departamentos",
                icon: mdiFolderAccount,
            },
        ],
    },
];
