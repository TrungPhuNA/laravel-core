# GitFlow & Jira Flow

## GitFlow

Read more: https://danielkummer.github.io/git-flow-cheatsheet/index.vi_VN.html

### Branch naming convention

```txt
feature/[FEATURE_ID]_feature_name
bugfix/[BUG_ID]_bug_name
hotfix/[BUG_ID]_hot_fix_name
release/version_number_version_name
```

### Git commit message

```txt
[TASK_ID] Message of Git
```

### Git commit convention

- Không commit tất cả file vào một commit nếu các file không cùng một hạng mục.

### Release feature

- Tạo PR merge vào `STAGING`.
- Nếu cần data production để test staging thì request data.
- Tạo PR merge `STAGING` -> `MASTER` và deploy production.

### HotFix production

- Tạo branch `hotfix/...` từ `MASTER` và thực hiện hot-fix.
- Tạo PR merge vào `MASTER`.

## Jira flow

- (Điền quy trình team đang dùng)

