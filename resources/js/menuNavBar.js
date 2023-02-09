import {
    mdiMenu,
    mdiClockOutline,
    mdiCloud,
    mdiCrop,
    mdiAccount,
    mdiCogOutline,
    mdiEmail,
    mdiLogout,
    mdiThemeLightDark,
    mdiGithub,
    mdiReact,
} from "@mdi/js";

export default [
    // {
    //   icon: mdiMenu,
    //   label: "Sample menu",
    //   menu: [
    //     {
    //       icon: mdiClockOutline,
    //       label: "Item One",
    //     },
    //     {
    //       icon: mdiCloud,
    //       label: "Item Two",
    //     },
    //     {
    //       isDivider: true,
    //     },
    //     {
    //       icon: mdiCrop,
    //       label: "Item Last",
    //     },
    //   ],
    // },
    {
        icon: mdiThemeLightDark,
        label: "Light/Dark",
        isDesktopNoLabel: true,
        isToggleLightDark: true,
    },
    {
        isCurrentUser: true,
        menu: [
            {
                icon: mdiAccount,
                label: "Mi perfil",
                to: "profile",
            },
            {
                icon: mdiCogOutline,
                label: "Ajustes",
            },
            {
                icon: mdiEmail,
                label: "Mensajes",
            },
            {
                isDivider: true,
            },
            {
                icon: mdiLogout,
                label: "Cerrar sesión",
                isLogout: true,
            },
        ],
    },
];
