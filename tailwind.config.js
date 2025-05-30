import flowbitePlugin from "flowbite/plugin";

export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./node_modules/flowbite/**/*.js", // important for Flowbite
    ],
    theme: {
        extend: {},
    },
    plugins: [flowbitePlugin],
};
