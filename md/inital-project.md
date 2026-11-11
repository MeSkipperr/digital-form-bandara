# How to Use Git — Digital Form Bandara

This document explains how to clone the project, do your work on the branch for your role, and push your changes to GitHub.

Repository: [MeSkipperr/digital-form-bandara](https://github.com/MeSkipperr/digital-form-bandara)

Clone URLs:

```text
git@github.com:MeSkipperr/digital-form-bandara.git
https://github.com/MeSkipperr/digital-form-bandara.git
```

Use the SSH URL (`git@github.com:...`) if an SSH key is already added to your GitHub account. Otherwise use HTTPS.

## Branch layout

Each person works on a assigned development branch. Do not implement features directly on `main`.

| Role | Branch | What you work on |
| --- | --- | --- |
| Admin | `development/admin` | Features and pages for the admin role |
| Petugas | `development/petugas` | Features and pages for the petugas role |
| Everyone | `main` | Agreed shared code. Do not use this branch for daily work |

`main` is the shared starting point. `development/admin` and `development/petugas` start from the same commit as `main`. The person who owns each role then commits their work on that branch.

## 1. Clone the project

Do this once, in the folder where you keep your code.

```bash
git clone git@github.com:MeSkipperr/digital-form-bandara.git
cd digital-form-bandara
```

If you use HTTPS:

```bash
git clone https://github.com/MeSkipperr/digital-form-bandara.git
cd digital-form-bandara
```

That command downloads `main`. Continue to the next step so your work lands on the branch for your role.

## 2. Switch to your task branch

Fetch the branch list from GitHub, then check out the branch for your role.

Admin:

```bash
git fetch origin
git checkout development/admin
```

Petugas:

```bash
git fetch origin
git checkout development/petugas
```

Confirm you are on the correct branch before you edit files:

```bash
git branch
```

The current branch is marked with an asterisk (`*`). Example for admin:

```text
* development/admin
  main
```

From this point, all of your changes belong on that branch.

## 3. Do your work

Repeat this sequence every time you continue working.

1. Confirm you are still on your role branch (`git branch`).
2. Pull the latest changes from GitHub:

```bash
git pull
```

3. Edit the files for your task.
4. Review what changed:

```bash
git status
```

5. Save your changes as a commit. Write a message that describes the work, for example `add admin login form`.

```bash
git add .
git commit -m "add admin login form"
```

Do not commit secrets. `.env` is ignored by Git and must not be committed.

## 4. Push to GitHub

A push sends commits from your computer to the same branch on GitHub.

### Daily push

After you commit, and while `git branch` shows `development/admin` or `development/petugas`:

```bash
git push
```

Teammates on the same branch can then take those commits with `git pull`.

### First push for a branch

Do this once, when the branch has never been sent to GitHub. The command creates the branch on GitHub and remembers it as the upstream, so later `git push` and `git pull` do not need the branch name.

Admin:

```bash
git checkout development/admin
git push -u origin development/admin
```

Petugas:

```bash
git checkout development/petugas
git push -u origin development/petugas
```

`-u` means `--set-upstream`. After it succeeds, GitHub shows the branch on the repository page, and teammates with the same role can `git fetch` and then `git checkout` that branch.

### If Git asks you to sign in

- SSH: make sure your key is added to GitHub. Test with `ssh -T git@github.com`.
- HTTPS: GitHub no longer accepts your account password. When prompted, enter your GitHub username and a Personal Access Token as the password. Create a token in GitHub → Settings → Developer settings → Personal access tokens, with the `repo` scope.

### Check that the push worked

```bash
git status
```

The message `Your branch is up to date with 'origin/development/admin'` (or `origin/development/petugas`) means the commits are on GitHub.

## Short flow per role

Admin:

```bash
git clone git@github.com:MeSkipperr/digital-form-bandara.git
cd digital-form-bandara
git fetch origin
git checkout development/admin
# do admin work
git add .
git commit -m "describe the change"
git push
```

Petugas:

```bash
git clone git@github.com:MeSkipperr/digital-form-bandara.git
cd digital-form-bandara
git fetch origin
git checkout development/petugas
# do petugas work
git add .
git commit -m "describe the change"
git push
```

On the first clone, if `git checkout` says the branch does not exist, that branch has not been pushed yet. Ask the branch owner to run the first-push commands in section 4, then run `git fetch origin` and `git checkout` again.
