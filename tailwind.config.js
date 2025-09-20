import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
    ],

    theme: {
        extend: {
            fontFamily: {
                display: ["Poppins", ...defaultTheme.fontFamily.sans],
                body: ["Inter", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                deped: {
                    50: "#eef7ff",
                    100: "#d9edff",
                    200: "#bce0ff",
                    300: "#8accff",
                    400: "#4cb3ff",
                    500: "#1f91ff",
                    600: "#0072e6", // DepEd Blue
                    700: "#005bb8",
                    800: "#004a96",
                    900: "#003d7a",
                },
                gold: {
                    50: "#fff9eb",
                    100: "#ffefc6",
                    200: "#ffd979",
                    300: "#ffc53d", // DepEd Gold
                    400: "#ffb81c",
                    500: "#ff9f00",
                    600: "#e27c00",
                    700: "#bb5802",
                    800: "#984808",
                    900: "#7c3b0a",
                },
                red: "#D12027", // DepEd Red
                white: "#FFFFFF",
                gray: {
                    50: "#f8f9fa",
                    100: "#f1f3f5",
                    200: "#e9ecef",
                    300: "#dee2e6",
                    400: "#ced4da",
                    500: "#adb5bd",
                    600: "#868e96",
                    700: "#495057",
                    800: "#343a40",
                    900: "#212529",
                },
            },
            boxShadow: {
                soft: "0 10px 30px rgba(0,0,0,0.08)",
            },
        },
    },

    plugins: [forms],
};
