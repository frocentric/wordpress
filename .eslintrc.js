module.exports = {
  parser: "@typescript-eslint/parser",
  extends: ["eslint:recommended", "prettier"],
  env: {
    es6: true,
    node: true,
  },
  rules: {
    "prettier/prettier": "error",
  },
  plugins: ["prettier"],
};
