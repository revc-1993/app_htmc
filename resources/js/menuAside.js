import {
    mdiAccountCircle,
    mdiHome,
    mdiFileSign,
    mdiLock,
    mdiAlertCircle,
    mdiSquareEditOutline,
    mdiCardBulleted,
    mdiViewList,
    mdiTelevisionGuide,
    mdiResponsive,
    mdiPalette,
} from "@mdi/js";

export default [
    {
        route: "dashboard",
        icon: mdiHome,
        label: "Dashboard",
    },
    {
        route: "certifications.index",
        label: "Certificaciones",
        icon: mdiCardBulleted,
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
