const universalRegex = /(Version:\s+)(\d\.\d\.\d)($|\n)/m;
const pluginRegex =
  /(define\(\s?(?:'|")PLUGIN_NAME_VERSION(?:'|"),\s?(?:'|"))(\d\.\d\.\d)('|")/m;

module.exports.readVersion = function (contents) {
  const match = contents.match(universalRegex);

  return match ? match[2] : null;
};

module.exports.writeVersion = function (contents, version) {
  return contents
    .replace(universalRegex, `$1${version}$3`)
    .replace(pluginRegex, `$1${version}$3`);
};
