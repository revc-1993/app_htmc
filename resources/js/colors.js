export const gradientBgBase = "bg-gradient-to-tr";
export const gradientBgPurplePink = `${gradientBgBase} from-purple-400 via-pink-500 to-red-500`;
export const gradientBgDark = `${gradientBgBase} from-slate-700 via-slate-900 to-slate-800`;
export const gradientBgPinkRed = `${gradientBgBase} from-pink-400 via-red-500 to-yellow-500`;
export const gradientBgSlateGray = `${gradientBgBase} from-slate-400 via-gray-300 to-gray-500`;

export const colorsBgLight = {
    white: "bg-white text-black",
    light: "bg-white text-black dark:bg-slate-900/70 dark:text-white",
    slate:"bg-gray-500 border-gray-500 dark:bg-gray-200 dark:text-black",
    contrast: "bg-gray-800 text-white dark:bg-white dark:text-black",
    success: "bg-emerald-500 border-emerald-500 text-white",
    danger: "bg-red-500 border-red-500 text-white",
    warning: "bg-yellow-500 border-yellow-500 text-white",
    info: "bg-blue-500 border-blue-500 text-white",
    green: "bg-green-500 border-green-500 text-white",
    teal: "bg-teal-500 border-teal-500 text-white",
    violet: "bg-violet-500 border-violet-500 text-white",
    orange: "bg-orange-500 border-orange-500 text-white",
};

export const colorsText = {
    white: "text-black dark:text-slate-100",
    light: "text-gray-700 dark:text-slate-400",
    slate: "text-gray-800 dark:text-slate-400",
    contrast: "dark:text-white",
    success: "text-emerald-500",
    danger: "text-red-500",
    warning: "text-yellow-500",
    info: "text-blue-500",
    green: "text-green-500",
    teal: "text-teal-500",
    violet: "text-violet-500",
    orange: "text-orange-500",
};

export const colorsOutline = {
    white: [colorsText.white, "border-gray-100"],
    light: [colorsText.light, "border-gray-100"],
    slate: [colorsText.slate, "border-gray-500"],
    contrast: [colorsText.contrast, "border-gray-900 dark:border-slate-100"],
    success: [colorsText.success, "border-emerald-500"],
    danger: [colorsText.danger, "border-red-500"],
    warning: [colorsText.warning, "border-yellow-500"],
    info: [colorsText.info, "border-blue-500"],
    green: [colorsText.green, "border-green-500"],
    teal: [colorsText.teal, "border-teal-500"],
    violet: [colorsText.violet, "border-violet-500"],
    orange: [colorsText.orange, "border-orange-500"],
};

export const getButtonColor = (
    color,
    isOutlined,
    hasHover,
    isActive = false
) => {
    const colors = {
        ring: {
            white: "ring-gray-200 dark:ring-gray-500",
            whiteDark: "ring-gray-200 dark:ring-gray-500",
            lightDark: "ring-gray-200 dark:ring-gray-500",
            slate: "ring-gray-500 dark:ring-gray-400",
            contrast: "ring-gray-300 dark:ring-gray-400",
            success: "ring-emerald-300 dark:ring-emerald-700",
            danger: "ring-red-300 dark:ring-red-700",
            warning: "ring-yellow-300 dark:ring-yellow-700",
            info: "ring-blue-300 dark:ring-blue-700",
            green: "ring-green-300 dark:ring-green-700",
            teal: "ring-teal-300 dark:ring-teal-700",
            violet: "ring-violet-300 dark:ring-violet-700",
            orange: "ring-orange-300 dark:ring-orange-700",
        },
        active: {
            white: "bg-gray-100",
            whiteDark: "bg-gray-100 dark:bg-slate-800",
            lightDark: "bg-gray-200 dark:bg-slate-700",
            slate: "bg-gray-700 dark:bg-slate-100",
            contrast: "bg-gray-700 dark:bg-slate-100",
            success: "bg-emerald-700 dark:bg-emerald-600",
            danger: "bg-red-700 dark:bg-red-600",
            warning: "bg-yellow-700 dark:bg-yellow-600",
            info: "bg-blue-700 dark:bg-blue-600",
            green: "bg-green-700 dark:bg-green-700",
            teal: "bg-teal-700 dark:bg-teal-700",
            violet: "bg-violet-700 dark:bg-violet-700",
            orange: "bg-orange-700 dark:bg-orange-700",
        },
        bg: {
            white: "bg-white text-black",
            whiteDark: "bg-white text-black dark:bg-slate-900 dark:text-white",
            lightDark:
                "bg-gray-100 text-black dark:bg-slate-800 dark:text-white",
            slate: "bg-gray-600 dark:bg-gray-500 text-white dark:text-black",
            contrast: "bg-gray-800 text-white dark:bg-white dark:text-black",
            success: "bg-emerald-600 dark:bg-emerald-500 text-white",
            danger: "bg-red-600 dark:bg-red-500 text-white",
            warning: "bg-yellow-600 dark:bg-yellow-500 text-white",
            info: "bg-blue-600 dark:bg-blue-500 text-white",
            green: "bg-green-600 dark:bg-green-500 text-white",
            teal: "bg-teal-600 dark:bg-teal-500 text-white",
            violet: "bg-violet-600 dark:bg-violet-500 text-white",
            orange: "bg-orange-600 dark:bg-orange-500 text-white",
        },
        bgHover: {
            white: "hover:bg-gray-100",
            whiteDark: "hover:bg-gray-100 hover:dark:bg-slate-800",
            lightDark: "hover:bg-gray-200 hover:dark:bg-slate-700",
            contrast: "hover:bg-gray-700 hover:dark:bg-slate-100",
            slate:
                "hover:bg-gray-700 hover:border-gray-700 hover:dark:bg-gray-600 hover:dark:border-gray-600",
            success:
                "hover:bg-emerald-700 hover:border-emerald-700 hover:dark:bg-emerald-600 hover:dark:border-emerald-600",
            danger: "hover:bg-red-700 hover:border-red-700 hover:dark:bg-red-600 hover:dark:border-red-600",
            warning:
                "hover:bg-yellow-700 hover:border-yellow-700 hover:dark:bg-yellow-600 hover:dark:border-yellow-600",
            info: "hover:bg-blue-700 hover:border-blue-700 hover:dark:bg-blue-600 hover:dark:border-blue-600",
            green: "hover:bg-green-700 hover:border-green-700 hover:dark:bg-green-600 hover:dark:border-green-600",
            teal: "hover:bg-teal-700 hover:border-teal-700 hover:dark:bg-teal-600 hover:dark:border-teal-600",
            violet: "hover:bg-violet-700 hover:border-violet-700 hover:dark:bg-violet-600 hover:dark:border-violet-600",
            orange: "hover:bg-orange-700 hover:border-orange-700 hover:dark:bg-orange-600 hover:dark:border-orange-600",
        },
        borders: {
            white: "border-white",
            whiteDark: "border-white dark:border-slate-900",
            lightDark: "border-gray-100 dark:border-slate-800",
            contrast: "border-gray-800 dark:border-white",
            slate: "border-gray-600 dark:border-gray-500",
            success: "border-emerald-600 dark:border-emerald-500",
            danger: "border-red-600 dark:border-red-500",
            warning: "border-yellow-600 dark:border-yellow-500",
            info: "border-blue-600 dark:border-blue-500",
            green: "border-green-600 dark:border-green-500",
            teal: "border-teal-600 dark:border-teal-500",
            violet: "border-violet-600 dark:border-violet-500",
            orange: "border-orange-600 dark:border-orange-500",
        },
        text: {
            contrast: "dark:text-slate-100",
            slate: "text-gray-600 dark:text-gray-500",
            success: "text-emerald-600 dark:text-emerald-500",
            danger: "text-red-600 dark:text-red-500",
            warning: "text-yellow-600 dark:text-yellow-500",
            info: "text-blue-600 dark:text-blue-500",
            green: "text-green-600 dark:text-green-500",
            teal: "text-teal-600 dark:text-teal-500",
            violet: "text-violet-600 dark:text-violet-500",
            orange: "text-orange-600 dark:text-orange-500",
        },
        outlineHover: {
            contrast:
                "hover:bg-gray-800 hover:text-gray-100 hover:dark:bg-slate-100 hover:dark:text-black",
            slate:
                "hover:bg-gray-600 hover:text-white hover:text-white hover:dark:text-white hover:dark:border-gray-600",
            success:
                "hover:bg-emerald-600 hover:text-white hover:text-white hover:dark:text-white hover:dark:border-emerald-600",
            danger: "hover:bg-red-600 hover:text-white hover:text-white hover:dark:text-white hover:dark:border-red-600",
            warning:
                "hover:bg-yellow-600 hover:text-white hover:text-white hover:dark:text-white hover:dark:border-yellow-600",
            info: "hover:bg-blue-600 hover:text-white hover:dark:text-white hover:dark:border-blue-600",
            green: "hover:bg-green-600 hover:text-white hover:dark:text-white hover:dark:border-green-600",
            teal: "hover:bg-teal-600 hover:text-white hover:dark:text-white hover:dark:border-teal-600",
            violet: "hover:bg-violet-600 hover:text-white hover:dark:text-white hover:dark:border-violet-600",
            orange: "hover:bg-orange-600 hover:text-white hover:dark:text-white hover:dark:border-orange-600",
        },
    };

    if (!colors.bg[color]) {
        return color;
    }

    const isOutlinedProcessed =
        isOutlined && ["white", "whiteDark", "lightDark"].indexOf(color) < 0;

    const base = [colors.borders[color], colors.ring[color]];

    if (isActive) {
        base.push(colors.active[color]);
    } else {
        base.push(isOutlinedProcessed ? colors.text[color] : colors.bg[color]);
    }

    if (hasHover) {
        base.push(
            isOutlinedProcessed
                ? colors.outlineHover[color]
                : colors.bgHover[color]
        );
    }

    return base;
};
