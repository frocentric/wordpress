const pluginFiles = [
  {
    filename: "./web/app/plugins/froware/froware.php",
    updater: require("./scripts/js/wp-version-updater"),
  },
];
const themeFiles = [
  {
    filename: "./web/app/themes/frocentric/style.css",
    updater: require("./scripts/js/wp-version-updater"),
  },
  {
    filename: "./web/app/themes/frocentric-tech/style.css",
    updater: require("./scripts/js/wp-version-updater"),
  },
];

module.exports = {
  bumpFiles: pluginFiles.concat(themeFiles),
  packageFiles: pluginFiles,
};
