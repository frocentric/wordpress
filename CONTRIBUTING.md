# Contributing for Frocentric

The following is a set of guidelines for contributing to the Frocentric Wordpress codebase, which are hosted in the [Frocentric Organisation](https://github.com/frocentric) on GitHub. These are mostly guidelines, not rules. Use your best judgment, and feel free to propose changes to this document in a pull request.

## Code of Conduct

This project and everyone participating in it is governed by the [Frocentric Code of Conduct](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code. Please report unacceptable behavior to [tech.team@frocentric.io](mailto:tech.team@frocentric.io).

## Issues

All project issues are [created](https://github.com/frocentric/wordpress/issues/new/choose), discussed and managed within the [repository](https://github.com/frocentric/wordpress/issues). Ad-hoc or real-time issue conversations may take place within the project chat group as appropriate.

## Making Changes

Frocentric uses [GitLab Flow](https://docs.gitlab.com/ee/workflow/gitlab_flow.html) for commit management. Examples below are based on the bash commandline environment, but you may use your Git client of preference, e.g. [GitHub Desktop](https://desktop.github.com), [Tower](https://www.git-tower.com), etc.

### Step 1: Clone

To get started with your local development environment, you should first clone the main branch of the repository:

```
git clone git@github.com:frocentric/wordpress.git
cd wordpress
```

### Step 2: Run Composer

```
composer install
```

### Step 3: Branch

Create local feature branches to manage your work. These should be branched directly off of the `main` branch and named as "issue-[issue number]".

```sh
$ git checkout -b issue-XX
```

### Step 4: Code

Developing in the Frocentric codebase is best conducted with an IDE like VS Code or PHPStorm, which provide code management, debugging and other helpful functionality.

#### WordPress Updates
As our WordPress configuration is managed via Composer, extensions like plugins and themes can't be installed or updated freely via the WordPress control panel. Installing a new extension requires adding the relevant entry to composer.json and (also if updating extensions) then executing:

```sh
$ composer update
```

### Step 5: Commit

It is recommended to keep your changes grouped logically within individual commits. When reviewing or maintaining code, it's often easier to review segregated changes that are split across multiple commits.

```sh
$ git add my/changed/files
$ git commit
```

#### Commit message guidelines

A good commit message should describe what changed and why. Frocentric projects
follow the [Conventional Commits](https://conventionalcommits.org/) specification to streamline documentation
and maintenance.

Examples of commit messages with semantic prefixes:

- `fix: don't overwrite prevent_default if default wasn't prevented`
- `feat: add app.isPackaged() method`
- `chore: beautifl site-specific plugin now activated by default`

Accepted prefixes:

  - `fix`: A bug fix
  - `feat`: A new feature
  - `docs`: Documentation changes
  - `test`: Adding missing tests or correcting existing tests
  - `build`: Changes that affect the build system
  - `ci`: Changes to our CI configuration files and scripts
  - `perf`: A code change that improves performance
  - `refactor`: A code change that neither fixes a bug nor adds a feature
  - `style`: Changes that do not affect the meaning of the code (linting)
  - `vendor`: Bumping a dependency like libchromiumcontent or node

Other things to keep in mind when writing a commit message:

1. The first line should:
   - contain a short description of the change (50 characters or less)
   - be entirely in lowercase with the exception of proper nouns, acronyms, and
   the words that refer to code, like function/variable names
2. Keep the second line blank.
3. Wrap all other lines at 72 columns.

#### Breaking Changes

A commit that has the text `BREAKING CHANGE:` at the beginning of its optional
body or footer section introduces a breaking API change (correlating with Major
in semantic versioning). A breaking change can be part of commits of any type.
e.g., a `fix:`, `feat:` & `chore:` types would all be valid, in addition to any
other type.

See [conventionalcommits.org](https://conventionalcommits.org) for more details.

#### Tooling

We use [Commitizen](https://commitizen.github.io/cz-cli) and [commitlint](https://commitlint.js.org/#/) to support the creation of compliant commit messages. The following extensions are recommended for commits from IDEs/editors:

- [Conventional Commits](https://marketplace.visualstudio.com/items?itemName=vivaxy.vscode-conventional-commits)
for VS Code.
- [Conventional Commits](https://github.com/lppedd/idea-conventional-commit) for
JetBrains IDEs (PHPStorm, WebStorm, etc)

### Step 6: Merge

Once you have committed your changes, you should then use `git merge` to synchronize your branch with the latest changes from the GitHub repository.

```sh
$ git fetch origin
$ git merge origin/main
```

This ensures that your working branch has the latest changes from the `main` branch.

### Step 7: Push
Once your commits are ready to go, push your working branch to GitHub.

```sh
$ git push origin issue-XX
```

### Step 8: Issue Pull Request

Once your changes are ready to be deployed, [submit a pull request](https://docs.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request#creating-the-pull-request) to the `main` branch, where it will be reviewed and merged or passed back for any requested changes.

**Note:** The standard Git commandline doesn't support making a pull request on GitHub, so you must use the web interface or a desktop client for this step

### Step 9: Review

After being merged to `main`, a [GitHub Action](https://github.com/frocentric/wordpress/actions)
is configured that automatically deploys the code to the staging website. Once the pull request is merged,
the deployment will take 2-3 minutes to execute, after which you can review and test your changes in that environment.
