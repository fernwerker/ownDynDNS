# Agent Guidelines

This repository supports agent descriptions for automated verification, remote testing, and other automated workflows.

## Generic agent principles

- Keep sensitive information out of source control.
- Store secrets and webhook URLs in local configuration files such as `.env`.
- Reference generic rules from `AGENT.md` whenever possible.

## Versioning

- Use semantic versioning: `MAJOR.MINOR.PATCH`.
- `MAJOR` for incompatible API changes.
- `MINOR` for backward-compatible functionality additions.
- `PATCH` for backward-compatible bug fixes.
- Optional build metadata may be appended for development validation:
  - `1.2.3+build.42`

## Local version publication

- `version.php` must expose a plain version string.
- Examples:
  - `0.1.0`
  - `0.1.0+build.1`

## Manual validation steps (example)

Run these commands from the repository root to update the build suffix and push.

```bash
# update the build suffix in version.php (edit the file or use a script)
git add version.php AGENT.md README.md
git commit -m "ci: bump build suffix for validation" || true
git push
```
