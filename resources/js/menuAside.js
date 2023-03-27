import {
    mdiAccountCircle,
    mdiMonitor,
    mdiShieldCrown,
    mdiTextBoxMultipleOutline,
    mdiCash,
    mdiNotebook,
    mdiFileDocumentEditOutline,
    mdiCardBulleted,
    mdiFolderAccount,
    mdiCheckbook,
    mdiAccountGroup,
    mdiBadgeAccountHorizontal,
    mdiRefresh,
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
        icon: mdiCardBulleted,
    },
    {
        route: "commitments.index",
        label: "Compromisos",
        icon: mdiTextBoxMultipleOutline,
    },
    {
        to: "accruals.index",
        label: "Devengos",
        icon: mdiFileDocumentEditOutline,
    },
    {
        to: "accruals.index",
        label: "Anticipos",
        icon: mdiRefresh,
    },
    {
        to: "accruals.index",
        label: "Pagos",
        icon: mdiCash,
    },
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
        icon: mdiAccountCircle,
    },
    {
        label: "Administrador",
        icon: mdiShieldCrown,
        menu: [
            {
                route: "users.index",
                label: "Usuarios",
                icon: mdiAccountGroup,
            },
            {
                // route: "",
                label: "Roles",
                icon: mdiBadgeAccountHorizontal,
            },
            {
                label: "Catálogo",
                icon: mdiNotebook,
                menu: [
                    {
                        // route: "",
                        label: "Departamentos",
                        icon: mdiFolderAccount,
                    },
                    {
                        // route: "",
                        label: "Tipos de gasto",
                        icon: mdiCheckbook,
                    },
                ],
            },
        ],
    },
    // {
    //     to: "/login",
    //     label: "Login",
    //     icon: mdiLock,
    // },
    // {
    //     to: "/error",
    //     label: "Error",
    //     icon: mdiAlertCircle,
    // },
    // {
    //     label: "Dropdown",
    //     icon: mdiViewList,
    //     menu: [
    //         {
    //             label: "Item One",
    //         },
    //         {
    //             label: "Item Two",
    //         },
    //     ],
    // },
];
