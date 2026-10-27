# API v1

Current public endpoints:
- `GET /api/v1/public/site.php` — public site settings subset
- `GET /api/v1/jobs/list.php?limit=20&offset=0` — published jobs
- `GET /api/v1/jobs/detail.php?slug=job-slug` — published job details
- `POST /api/v1/contact/create.php` — contact message JSON or form body
- `POST /api/v1/applications/create.php` — intentionally returns 501 until upload policy, rate limiting and API security are configured

Use HTTPS in production. Do not expose admin or applicant data through public endpoints. Add rate limiting and monitoring before public launch.
