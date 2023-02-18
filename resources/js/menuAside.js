import {
    mdiAccountCircle,
    mdiMonitor,
    mdiFileSign,
    mdiTextBoxMultipleOutline,
    mdiCash,
    mdiAlertCircle,
    mdiFileDocumentEditOutline,
    mdiCardBulleted,
    mdiViewList,
    mdiTelevisionGuide,
    mdiResponsive,
    mdiPalette,
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
