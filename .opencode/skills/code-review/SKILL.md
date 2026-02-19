---
name: code-review
description: >-
  Performs code review on written or edited code. Activates when writing new code, editing existing code,
  or when user requests code review. Ensures code quality, security, and license compliance.
---

# Code Review

## When to Apply

Activate this skill when:

- Writing new code files
- Editing existing code
- User explicitly requests code review
- Adding new dependencies or packages

## Code Review Checklist

### 1. License Compliance (Critical)

Before adding any external code, library, or package:

- **NEVER use GPL, AGPL, LGPL, or any copyleft licensed code** without explicit approval
- Prefer MIT, BSD, Apache 2.0, or permissive licenses
- Check `composer.json` for license information of dependencies
- Verify license compatibility with the project's MIT license

**Prohibited Licenses:**
- GPL v2/v3
- AGPL v3
- LGPL v2/v3
- SSPL
- Commons Clause
- Business Source License (BSL) with restrictions

**Allowed Licenses:**
- MIT
- BSD (2-clause, 3-clause)
- Apache 2.0
- ISC
- PHP License
- CC0
- Unlicense

### 2. Code Quality

- Follow project's code style (Laravel Pint for PHP, ESLint for JS)
- Use meaningful variable and function names
- Add appropriate PHPDoc/JSdoc comments for complex logic
- Keep functions focused and single-purpose
- Remove commented-out code before finalizing

### 3. Security

- Validate all user inputs
- Use parameterized queries to prevent SQL injection
- Sanitize output to prevent XSS
- Never expose sensitive information in logs or error messages
- Use environment variables for secrets

### 4. Laravel Best Practices

- Use Eloquent over raw SQL queries
- Implement proper eager loading to avoid N+1 queries
- Use Form Request classes for validation
- Follow Laravel naming conventions
- Use dependency injection

### 5. Performance

- Avoid N+1 query problems
- Use caching where appropriate
- Lazy load relationships when possible
- Optimize database queries with proper indexes

### 6. Testing

- Ensure new functionality has test coverage
- Follow Pest testing conventions
- Test edge cases and error conditions

## Review Process

When reviewing code:

1. Check license compatibility first
2. Verify code follows project conventions
3. Look for security vulnerabilities
4. Check for performance issues
5. Ensure proper error handling
6. Verify test coverage

## Reporting Issues

If issues are found during review, report them clearly:

- Specify file and line number
- Explain the issue
- Suggest a solution
- Mark license issues as BLOCKING
