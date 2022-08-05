const pluginFiles = [
  {
    filename: './web/app/plugins/froware/froware.php',
    updater: './scripts/js/wp-version-updater.js',
  },
];
const themeFiles = [
  {
    filename: './web/app/themes/frocentric/style.css',
    updater: './scripts/js/wp-version-updater.js',
  },
  {
    filename: './web/app/themes/frocentric-tech/style.css',
    updater: './scripts/js/wp-version-updater.js',
  },
];

module.exports = {
  bumpFiles: pluginFiles.concat(themeFiles),
  packageFiles: pluginFiles,
};
